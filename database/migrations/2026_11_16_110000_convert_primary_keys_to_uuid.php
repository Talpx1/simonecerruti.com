<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Morph classes are frozen as strings: they are the values already stored in the morph type columns.
     *
     * @var array<string, string>
     */
    private const array MODEL_TABLES = [
        'users' => 'App\Models\User',
        'clients' => 'App\Models\Client',
        'blog_articles' => 'App\Models\BlogArticle',
        'blog_article_relatables' => 'App\Models\BlogArticleRelatable',
        'campaigns' => 'App\Models\Campaign',
        'contact_leads' => 'App\Models\ContactLead',
        'leads' => 'App\Models\Lead',
        'page_views' => 'App\Models\PageView',
        'projects' => 'App\Models\Project',
        'seo' => 'App\Models\Seo',
        'seo_settings' => 'App\Models\SeoSetting',
    ];

    /** @var list<array{table: string, column: string, references: string}> */
    private const array FOREIGN_COLUMNS = [
        ['table' => 'blog_articles', 'column' => 'author_id', 'references' => 'users'],
        ['table' => 'blog_article_relatables', 'column' => 'blog_article_id', 'references' => 'blog_articles'],
        ['table' => 'projects', 'column' => 'client_id', 'references' => 'clients'],
        ['table' => 'visit_sessions', 'column' => 'campaign_id', 'references' => 'campaigns'],
        ['table' => 'sessions', 'column' => 'user_id', 'references' => 'users'],
    ];

    /** @var list<array{table: string, morph: string}> */
    private const array MORPH_COLUMNS = [
        ['table' => 'blog_article_relatables', 'morph' => 'relatable'],
        ['table' => 'leads', 'morph' => 'leadable'],
        ['table' => 'seo', 'morph' => 'seoable'],
        ['table' => 'media', 'morph' => 'model'],
        ['table' => 'taggables', 'morph' => 'taggable'],
        ['table' => 'activity_log', 'morph' => 'subject'],
        ['table' => 'activity_log', 'morph' => 'causer'],
    ];

    /**
     * Foreign keys stored inside the activity log attribute changes, per subject class: a table name for a plain key, a `morph:` prefix for a morph key.
     *
     * @var array<string, array<string, string>>
     */
    private const array LOGGED_FOREIGN_KEYS = [
        'App\Models\BlogArticle' => ['author_id' => 'users'],
        'App\Models\BlogArticleRelatable' => ['blog_article_id' => 'blog_articles', 'relatable_id' => 'morph:relatable_type'],
        'App\Models\Project' => ['client_id' => 'clients'],
        'App\Models\Lead' => ['leadable_id' => 'morph:leadable_type'],
        'App\Models\Seo' => ['seoable_id' => 'morph:seoable_type'],
    ];

    private const string TEMPORARY_SUFFIX = '_uuid';

    public function up(): void {
        $affected_tables = \array_values(\array_unique([
            ...\array_keys(self::MODEL_TABLES),
            ...\array_column(self::FOREIGN_COLUMNS, 'table'),
            ...\array_column(self::MORPH_COLUMNS, 'table'),
        ]));

        $constraints = $this->getConstraints($affected_tables);
        $indexes = $this->getIndexes($affected_tables);

        // Session payloads hold the old integer user id, so authenticated sessions must log in again.
        DB::table('sessions')->whereNotNull('user_id')->delete();

        $this->keepLegacyMediaPaths();

        foreach (\array_keys(self::MODEL_TABLES) as $table) {
            $this->addOrderedUuidColumn($table);
        }

        foreach (self::FOREIGN_COLUMNS as $foreign_column) {
            $this->convertForeignColumn($foreign_column['table'], $foreign_column['column'], $foreign_column['references']);
        }

        foreach (self::MORPH_COLUMNS as $morph_column) {
            $this->convertMorphColumn($morph_column['table'], $morph_column['morph']);
        }

        $this->remapActivityLogAttributeChanges();

        foreach (\array_keys(self::MODEL_TABLES) as $table) {
            $this->replacePrimaryKey($table);
        }

        $this->restoreConstraints($constraints, ['p', 'u']);
        $this->restoreIndexes($indexes);
        $this->restoreConstraints($constraints, ['f']);
    }

    /**
     * @param  list<string>  $tables
     * @return list<object{name: string, table_name: string, type: string, definition: string}>
     */
    private function getConstraints(array $tables): array {
        $placeholders = \implode(', ', \array_fill(0, \count($tables), '?'));

        /** @var list<object{name: string, table_name: string, type: string, definition: string}> */
        return DB::select(<<<SQL
            SELECT c.conname AS name, t.relname AS table_name, c.contype AS type, pg_get_constraintdef(c.oid) AS definition
            FROM pg_constraint c
            JOIN pg_class t ON t.oid = c.conrelid
            LEFT JOIN pg_class r ON r.oid = c.confrelid
            WHERE c.contype IN ('p', 'u', 'f')
                AND t.relnamespace = current_schema()::regnamespace
                AND (t.relname IN ({$placeholders}) OR r.relname IN ({$placeholders}))
            SQL, [...$tables, ...$tables]);
    }

    /**
     * @param  list<string>  $tables
     * @return list<object{name: string, definition: string}>
     */
    private function getIndexes(array $tables): array {
        $placeholders = \implode(', ', \array_fill(0, \count($tables), '?'));

        /** @var list<object{name: string, definition: string}> */
        return DB::select(<<<SQL
            SELECT i.relname AS name, pg_get_indexdef(i.oid) AS definition
            FROM pg_index x
            JOIN pg_class i ON i.oid = x.indexrelid
            JOIN pg_class t ON t.oid = x.indrelid
            WHERE t.relnamespace = current_schema()::regnamespace
                AND t.relname IN ({$placeholders})
                AND NOT EXISTS (
                    SELECT 1 FROM pg_constraint c
                    WHERE c.conindid = x.indexrelid AND c.conrelid = x.indrelid AND c.contype IN ('p', 'u', 'x')
                )
            SQL, $tables);
    }

    /**
     * Media files are stored under the owner's old integer id, so the path generator keeps reading it from here.
     */
    private function keepLegacyMediaPaths(): void {
        Schema::table('media', function (Blueprint $table): void {
            $table->unsignedBigInteger('legacy_model_id')->nullable();
        });

        DB::update('UPDATE media SET legacy_model_id = model_id');
    }

    /**
     * UUIDv7 values whose timestamps grow with the old id and stay before now, so ordering by id is preserved, also against new rows.
     */
    private function addOrderedUuidColumn(string $table): void {
        $uuid_column = 'id'.self::TEMPORARY_SUFFIX;

        Schema::table($table, function (Blueprint $blueprint) use ($uuid_column): void {
            $blueprint->uuid($uuid_column)->nullable();
        });

        DB::update(<<<SQL
            UPDATE {$table} AS target
            SET {$uuid_column} = numbered.uuid
            FROM (
                SELECT id, (
                    lpad(to_hex(floor(extract(epoch FROM now()) * 1000)::bigint - count(*) OVER () + row_number() OVER (ORDER BY id)), 12, '0')
                    || '7' || substr(random_hex, 14, 3) || substr(random_hex, 17, 16)
                )::uuid AS uuid
                FROM (SELECT id, replace(gen_random_uuid()::text, '-', '') AS random_hex FROM {$table}) AS randomized
            ) AS numbered
            WHERE numbered.id = target.id
            SQL);

        DB::statement("ALTER TABLE {$table} ALTER COLUMN {$uuid_column} SET NOT NULL");
    }

    private function convertForeignColumn(string $table, string $column, string $references): void {
        $uuid_column = $column.self::TEMPORARY_SUFFIX;
        $id_uuid_column = 'id'.self::TEMPORARY_SUFFIX;

        $this->addTemporaryUuidColumn($table, $uuid_column);

        DB::update(<<<SQL
            UPDATE {$table} AS referencing
            SET {$uuid_column} = referenced.{$id_uuid_column}
            FROM {$references} AS referenced
            WHERE referenced.id = referencing.{$column}
            SQL);

        $this->replaceColumn($table, $column, $uuid_column);
    }

    private function convertMorphColumn(string $table, string $morph): void {
        $column = "{$morph}_id";
        $type_column = "{$morph}_type";
        $uuid_column = $column.self::TEMPORARY_SUFFIX;
        $id_uuid_column = 'id'.self::TEMPORARY_SUFFIX;

        $this->addTemporaryUuidColumn($table, $uuid_column);

        foreach (self::MODEL_TABLES as $references => $morph_class) {
            DB::update(<<<SQL
                UPDATE {$table} AS referencing
                SET {$uuid_column} = referenced.{$id_uuid_column}
                FROM {$references} AS referenced
                WHERE referencing.{$type_column} = ? AND referenced.id = referencing.{$column}
                SQL, [$morph_class]);
        }

        $this->replaceColumn($table, $column, $uuid_column);
    }

    private function addTemporaryUuidColumn(string $table, string $uuid_column): void {
        Schema::table($table, function (Blueprint $blueprint) use ($uuid_column): void {
            $blueprint->uuid($uuid_column)->nullable();
        });
    }

    /**
     * Nullable columns keep NULL for orphan references; a NOT NULL column with an orphan reference aborts the whole migration.
     */
    private function replaceColumn(string $table, string $column, string $uuid_column): void {
        $is_nullable = collect(Schema::getColumns($table))->firstWhere('name', $column)['nullable'] ?? false;

        $orphans_count = DB::table($table)->whereNotNull($column)->whereNull($uuid_column)->count();

        throw_if(
            ! $is_nullable && $orphans_count > 0,
            RuntimeException::class,
            "Cannot convert {$table}.{$column} to uuid: {$orphans_count} rows reference a missing record."
        );

        Schema::table($table, function (Blueprint $blueprint) use ($column): void {
            $blueprint->dropColumn($column);
        });

        Schema::table($table, function (Blueprint $blueprint) use ($column, $uuid_column): void {
            $blueprint->renameColumn($uuid_column, $column);
        });

        if (! $is_nullable) {
            DB::statement("ALTER TABLE {$table} ALTER COLUMN {$column} SET NOT NULL");
        }
    }

    /**
     * Unmappable values (records deleted since they were logged) are left untouched: they are history only.
     */
    private function remapActivityLogAttributeChanges(): void {
        foreach (['attributes', 'old'] as $section) {
            foreach (self::MODEL_TABLES as $references => $morph_class) {
                $this->remapActivityLogKey($section, 'id', $references, 'a.subject_type = ?', [$morph_class]);

                foreach (self::LOGGED_FOREIGN_KEYS[$morph_class] ?? [] as $key => $target) {
                    if (! str_starts_with($target, 'morph:')) {
                        $this->remapActivityLogKey($section, $key, $target, 'a.subject_type = ?', [$morph_class]);

                        continue;
                    }

                    $type_key = \substr($target, \strlen('morph:'));

                    foreach (self::MODEL_TABLES as $morph_references => $referenced_class) {
                        $this->remapActivityLogKey(
                            $section,
                            $key,
                            $morph_references,
                            "a.subject_type = ? AND a.attribute_changes::jsonb #>> '{{$section},{$type_key}}' = ?",
                            [$morph_class, $referenced_class],
                        );
                    }
                }
            }
        }
    }

    /**
     * @param  list<string>  $bindings
     */
    private function remapActivityLogKey(string $section, string $key, string $references, string $condition, array $bindings): void {
        $id_uuid_column = 'id'.self::TEMPORARY_SUFFIX;
        $path = "'{{$section},{$key}}'";

        DB::update(<<<SQL
            UPDATE activity_log AS a
            SET attribute_changes = jsonb_set(a.attribute_changes::jsonb, {$path}, to_jsonb(referenced.{$id_uuid_column}::text))::json
            FROM {$references} AS referenced
            WHERE {$condition}
                AND a.attribute_changes::jsonb #>> {$path} ~ '^[0-9]+$'
                AND referenced.id = (a.attribute_changes::jsonb #>> {$path})::bigint
            SQL, $bindings);
    }

    private function replacePrimaryKey(string $table): void {
        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->dropColumn('id');
        });

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->renameColumn('id'.self::TEMPORARY_SUFFIX, 'id');
        });

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->primary('id');
        });
    }

    /**
     * Dropping a column silently drops every constraint built on it: re-create the missing ones exactly as they were.
     *
     * @param  list<object{name: string, table_name: string, type: string, definition: string}>  $constraints
     * @param  list<string>  $types
     */
    private function restoreConstraints(array $constraints, array $types): void {
        foreach ($constraints as $constraint) {
            if (! \in_array($constraint->type, $types, true)) {
                continue;
            }

            $is_existing = DB::selectOne(
                'SELECT 1 AS found FROM pg_constraint c JOIN pg_class t ON t.oid = c.conrelid WHERE c.conname = ? AND t.relname = ? AND t.relnamespace = current_schema()::regnamespace',
                [$constraint->name, $constraint->table_name],
            ) !== null;

            if (! $is_existing) {
                DB::statement("ALTER TABLE \"{$constraint->table_name}\" ADD CONSTRAINT \"{$constraint->name}\" {$constraint->definition}");
            }
        }
    }

    /**
     * @param  list<object{name: string, definition: string}>  $indexes
     */
    private function restoreIndexes(array $indexes): void {
        foreach ($indexes as $index) {
            $is_existing = DB::selectOne(
                'SELECT 1 AS found FROM pg_class WHERE relname = ? AND relnamespace = current_schema()::regnamespace',
                [$index->name],
            ) !== null;

            if (! $is_existing) {
                DB::statement($index->definition);
            }
        }
    }
};
