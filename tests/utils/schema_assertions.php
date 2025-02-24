<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

/**
 * Assert that a column in a table is nullable.
 *
 * If the provided table is a model class-string, the table name will be inferred from the model.
 */
function assertColumnIsNullable(string $table, string $column): void {
    test()->expect(getColumn($table, $column)['nullable'])->toBeTrue();
}

/**
 * Assert that a column in a table is not nullable.
 *
 * If the provided table is a model class-string, the table name will be inferred from the model.
 */
function assertColumnIsRequired(string $table, string $column): void {
    test()->expect(getColumn($table, $column)['nullable'])->toBeFalse();
}

/**
 * Assert that a table exists in the database.
 *
 * If the provided table is a model class-string, the table name will be inferred from the model.
 */
function assertDatabaseHasTable(string $table): void {
    test()->expect(Schema::hasTable(maybeGetTableNameForModel($table)))->toBeTrue();
}

/**
 * Assert that a table does not exist in the database.
 *
 * If the provided table is a model class-string, the table name will be inferred from the model.
 */
function assertDatabaseMissingTable(string $table): void {
    test()->expect(Schema::hasTable(maybeGetTableNameForModel($table)))->toBeFalse();
}

/**
 * Assert that a table has a one or more columns.
 *
 * If the provided table is a model class-string, the table name will be inferred from the model.
 */
function assertTableHasColumns(string $table, string ...$columns): void {
    test()->expect(Schema::getColumnListing(maybeGetTableNameForModel($table)))->toContain(...$columns);
}

/**
 * Assert that a table has a unique index.
 *
 * If the provided table is a model class-string, the table name will be inferred from the model.
 *
 * @param  string|string[]  $columns  The column(s) that make up the index. If the index is composed of multiple columns, pass them as an array.
 */
function assertIndexIsUnique(string $table, string|array $columns): void {
    if (! is_array($columns)) {
        $columns = [$columns];
    }

    $found = collect(Schema::getIndexes(maybeGetTableNameForModel($table)))
        ->contains(fn ($index): bool => $index['unique'] && sort($index['columns']) === sort($columns));

    test()->expect($found)->toBeTrue();
}
