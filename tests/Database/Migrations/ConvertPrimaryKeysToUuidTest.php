<?php

declare(strict_types=1);

use App\Media\MediaPathGenerator;
use App\Models\BlogArticle;
use App\Models\BlogArticleRelatable;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\ContactLead;
use App\Models\Lead;
use App\Models\PageView;
use App\Models\Project;
use App\Models\Seo;
use App\Models\SeoSetting;
use App\Models\User;
use App\Models\VisitSession;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Activity;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

const UUID_CONVERSION_MIGRATION = '2026_11_16_110000_convert_primary_keys_to_uuid';

const UUID_CONVERTED_TABLES = ['users', 'clients', 'blog_articles', 'blog_article_relatables', 'campaigns', 'contact_leads', 'leads', 'page_views', 'projects', 'seo', 'seo_settings'];

const UUID_COUNTED_TABLES = [...UUID_CONVERTED_TABLES, 'visit_sessions', 'media', 'tags', 'taggables', 'activity_log'];

// Postgres DDL is transactional, so RefreshDatabase rolls this rebuild back; migrate:fresh would purge the connection and escape the transaction.
beforeEach(function (): void {
    $legacy_migrations = collect([...glob(database_path('migrations/*.php')) ?: [], ...glob(database_path('settings/*.php')) ?: []])
        ->filter(fn (string $path): bool => basename($path, '.php') < UUID_CONVERSION_MIGRATION)
        ->values()
        ->all();

    Schema::dropAllTables();
    Artisan::call('migrate', ['--path' => $legacy_migrations, '--realpath' => true, '--force' => true]);

    expect(Schema::getColumnType('users', 'id'))->toBe('int8');

    DB::table('users')->insert([
        ['id' => 3, 'name' => 'Alice', 'email' => 'alice@example.test', 'password' => 'hash'],
        ['id' => 7, 'name' => 'Bob', 'email' => 'bob@example.test', 'password' => 'hash'],
    ]);
    DB::table('clients')->insert([['id' => 2, 'name' => 'Acme'], ['id' => 5, 'name' => 'Globex']]);
    DB::table('projects')->insert([
        ['id' => 2, 'title' => '{"en":"Project two"}', 'short_description' => '{}', 'description' => '{}', 'slug' => '{"en":"project-two"}', 'published' => true, 'featured' => false, 'client_id' => 5],
        ['id' => 4, 'title' => '{"en":"Project four"}', 'short_description' => '{}', 'description' => '{}', 'slug' => '{"en":"project-four"}', 'published' => true, 'featured' => true, 'client_id' => null],
        ['id' => 9, 'title' => '{"en":"Project nine"}', 'short_description' => '{}', 'description' => '{}', 'slug' => '{"en":"project-nine"}', 'published' => false, 'featured' => false, 'client_id' => 2],
    ]);
    DB::table('blog_articles')->insert([
        ['id' => 2, 'title' => '{"en":"Article two"}', 'slug' => '{"en":"article-two"}', 'summary' => '{}', 'content' => '{}', 'featured' => false, 'author_id' => 7, 'status' => 'draft'],
        ['id' => 4, 'title' => '{"en":"Article four"}', 'slug' => '{"en":"article-four"}', 'summary' => '{}', 'content' => '{}', 'featured' => true, 'author_id' => 3, 'status' => 'published'],
    ]);
    DB::table('blog_article_relatables')->insert([
        ['id' => 10, 'blog_article_id' => 2, 'relatable_type' => Project::class, 'relatable_id' => 4],
        ['id' => 11, 'blog_article_id' => 2, 'relatable_type' => BlogArticle::class, 'relatable_id' => 4],
        ['id' => 12, 'blog_article_id' => 4, 'relatable_type' => Project::class, 'relatable_id' => 2],
    ]);
    DB::table('campaigns')->insert(['id' => 6, 'name' => 'Spring', 'slug' => 'spring', 'source' => 'newsletter']);
    DB::table('visit_sessions')->insert([
        ['id' => '0190d6a0-0000-7000-8000-000000000001', 'source' => 'newsletter', 'campaign_id' => 6, 'landing_path' => '/', 'locale' => 'en', 'started_at' => now(), 'last_activity_at' => now()],
        ['id' => '0190d6a0-0000-7000-8000-000000000002', 'source' => 'direct', 'campaign_id' => null, 'landing_path' => '/', 'locale' => 'it', 'started_at' => now(), 'last_activity_at' => now()],
    ]);
    DB::table('page_views')->insert([
        ['id' => 100, 'visit_session_id' => '0190d6a0-0000-7000-8000-000000000001', 'url_path' => '/first', 'locale' => 'en', 'viewed_at' => now()],
        ['id' => 101, 'visit_session_id' => '0190d6a0-0000-7000-8000-000000000001', 'url_path' => '/second', 'locale' => 'en', 'viewed_at' => now()],
    ]);
    DB::table('contact_leads')->insert(['id' => 8, 'first_name' => 'Carla', 'last_name' => 'Lead', 'email' => 'lead@example.test', 'message' => 'Hello', 'acceptance' => true]);
    DB::table('leads')->insert(['id' => 1, 'leadable_type' => ContactLead::class, 'leadable_id' => 8, 'is_pinned' => true]);
    DB::table('seo_settings')->insert(['id' => 1, 'type' => 'person', 'title_separator' => '|', 'website_schema' => true]);
    DB::table('seo')->insert([
        ['id' => 1, 'seoable_type' => Project::class, 'seoable_id' => 2, 'title' => '{"en":"Project two SEO"}'],
        ['id' => 2, 'seoable_type' => BlogArticle::class, 'seoable_id' => 2, 'title' => '{"en":"Article two SEO"}'],
    ]);
    DB::table('tags')->insert(['id' => 1, 'name' => '{"en":"Laravel"}', 'slug' => '{"en":"laravel"}']);
    DB::table('taggables')->insert([
        ['tag_id' => 1, 'taggable_type' => Project::class, 'taggable_id' => 2],
        ['tag_id' => 1, 'taggable_type' => BlogArticle::class, 'taggable_id' => 2],
        ['tag_id' => 1, 'taggable_type' => Campaign::class, 'taggable_id' => 6],
    ]);
    DB::table('media')->insert([
        ['id' => 1, 'model_type' => Project::class, 'model_id' => 2, 'uuid' => '0190d6a0-0000-7000-8000-0000000000a1', 'collection_name' => 'featured_image', 'name' => 'cover', 'file_name' => 'cover.jpg', 'disk' => 'public', 'size' => 1, 'manipulations' => '[]', 'custom_properties' => '[]', 'generated_conversions' => '[]', 'responsive_images' => '[]'],
        ['id' => 2, 'model_type' => Client::class, 'model_id' => 5, 'uuid' => '0190d6a0-0000-7000-8000-0000000000a2', 'collection_name' => 'client_image', 'name' => 'logo', 'file_name' => 'logo.png', 'disk' => 'public', 'size' => 1, 'manipulations' => '[]', 'custom_properties' => '[]', 'generated_conversions' => '[]', 'responsive_images' => '[]'],
    ]);
    DB::table('activity_log')->insert([
        ['description' => 'created', 'event' => 'created', 'subject_type' => BlogArticle::class, 'subject_id' => 4, 'causer_type' => User::class, 'causer_id' => 3, 'attribute_changes' => '{"attributes":{"id":4,"author_id":3}}'],
        ['description' => 'deleted', 'event' => 'deleted', 'subject_type' => Project::class, 'subject_id' => 999, 'causer_type' => User::class, 'causer_id' => 7, 'attribute_changes' => '{"old":{"id":999,"client_id":2}}'],
        ['description' => 'updated', 'event' => 'updated', 'subject_type' => Lead::class, 'subject_id' => 1, 'causer_type' => null, 'causer_id' => null, 'attribute_changes' => '{"attributes":{"leadable_type":"App\\\\Models\\\\ContactLead","leadable_id":8}}'],
    ]);
    DB::table('sessions')->insert([
        ['id' => 'authenticated', 'user_id' => 3, 'payload' => 'payload', 'last_activity' => 1],
        ['id' => 'guest', 'user_id' => null, 'payload' => 'payload', 'last_activity' => 1],
    ]);

    $this->legacy_row_counts = collect(UUID_COUNTED_TABLES)->mapWithKeys(fn (string $table): array => [$table => DB::table($table)->count()])->all();
});

describe('conversion', function () {
    beforeEach(function (): void {
        Artisan::call('migrate', ['--force' => true]);
    });

    it('turns every app primary key into a uuid primary key', function (string $table) {
        $primary_index = collect(Schema::getIndexes($table))->firstWhere('primary', true);

        expect(Schema::getColumnType($table, 'id'))->toBe('uuid')
            ->and($primary_index['columns'] ?? null)->toBe(['id'])
            ->and(DB::table($table)->whereNull('id')->count())->toBe(0);
    })->with(UUID_CONVERTED_TABLES);

    it('keeps every row', function () {
        $row_counts = collect(UUID_COUNTED_TABLES)->mapWithKeys(fn (string $table): array => [$table => DB::table($table)->count()])->all();

        expect($row_counts)->toBe($this->legacy_row_counts)
            ->and(DB::table('sessions')->pluck('id')->all())->toBe(['guest']);
    });

    it('turns every reference to an app model into a uuid column', function (string $table, string $column) {
        expect(Schema::getColumnType($table, $column))->toBe('uuid');
    })->with([
        ['blog_articles', 'author_id'],
        ['blog_article_relatables', 'blog_article_id'],
        ['blog_article_relatables', 'relatable_id'],
        ['projects', 'client_id'],
        ['visit_sessions', 'campaign_id'],
        ['sessions', 'user_id'],
        ['leads', 'leadable_id'],
        ['seo', 'seoable_id'],
        ['media', 'model_id'],
        ['taggables', 'taggable_id'],
        ['activity_log', 'subject_id'],
        ['activity_log', 'causer_id'],
    ]);

    it('keeps the order of the old integer ids', function () {
        expect(Project::query()->orderBy('id')->get()->pluck('title')->all())->toBe(['Project two', 'Project four', 'Project nine'])
            ->and(PageView::query()->orderBy('id')->pluck('url_path')->all())->toBe(['/first', '/second']);
    });

    it('generates uuids for new rows after the converted ones', function () {
        $client = Client::factory()->create();

        expect(Client::query()->orderBy('id')->get()->last()?->is($client))->toBeTrue();
    });

    it('re-creates the foreign keys with their delete and update rules', function (string $table, string $column, string $foreign_table, string $on_delete, string $on_update) {
        $foreign_key = collect(Schema::getForeignKeys($table))->first(fn (array $key): bool => $key['columns'] === [$column]);

        expect($foreign_key)->not->toBeNull()
            ->and($foreign_key['foreign_table'])->toBe($foreign_table)
            ->and($foreign_key['foreign_columns'])->toBe(['id'])
            ->and($foreign_key['on_delete'])->toBe($on_delete)
            ->and($foreign_key['on_update'])->toBe($on_update);
    })->with([
        ['blog_articles', 'author_id', 'users', 'set null', 'cascade'],
        ['blog_article_relatables', 'blog_article_id', 'blog_articles', 'cascade', 'cascade'],
        ['projects', 'client_id', 'clients', 'no action', 'no action'],
        ['visit_sessions', 'campaign_id', 'campaigns', 'set null', 'no action'],
        ['page_views', 'visit_session_id', 'visit_sessions', 'cascade', 'no action'],
    ]);

    it('re-creates the unique and morph indexes', function (string $table, array $columns, bool $is_unique) {
        $has_index = collect(Schema::getIndexes($table))
            ->contains(fn (array $index): bool => $index['columns'] === $columns && $index['unique'] === $is_unique);

        expect($has_index)->toBeTrue();
    })->with([
        ['blog_article_relatables', ['blog_article_id', 'relatable_type', 'relatable_id'], true],
        ['blog_article_relatables', ['relatable_type', 'relatable_id'], false],
        ['seo', ['seoable_type', 'seoable_id'], true],
        ['leads', ['leadable_type', 'leadable_id'], false],
        ['media', ['model_type', 'model_id'], false],
        ['taggables', ['tag_id', 'taggable_id', 'taggable_type'], true],
        ['taggables', ['taggable_type', 'taggable_id'], false],
        ['activity_log', ['subject_type', 'subject_id'], false],
        ['activity_log', ['causer_type', 'causer_id'], false],
        ['sessions', ['user_id'], false],
        ['visit_sessions', ['campaign_id', 'started_at'], false],
    ]);

    it('keeps every belongs-to link on the same parent', function () {
        expect(BlogArticle::query()->with('author')->get()->mapWithKeys(fn (BlogArticle $article): array => [$article->title => $article->author->email])->all())
            ->toBe(['Article two' => 'bob@example.test', 'Article four' => 'alice@example.test'])
            ->and(Project::query()->with('client')->get()->mapWithKeys(fn (Project $project): array => [$project->title => $project->client?->name])->all())
            ->toEqualCanonicalizing(['Project two' => 'Globex', 'Project four' => null, 'Project nine' => 'Acme'])
            ->and(VisitSession::query()->with('campaign')->get()->mapWithKeys(fn (VisitSession $session): array => [$session->source => $session->campaign?->slug])->all())
            ->toEqualCanonicalizing(['newsletter' => 'spring', 'direct' => null])
            ->and(PageView::query()->with('visitSession')->get()->pluck('visitSession.source')->unique()->all())->toBe(['newsletter']);
    });

    it('keeps every morph link on the same parent', function () {
        $relatables = BlogArticleRelatable::query()->with(['article', 'relatable'])->get()
            ->map(fn (BlogArticleRelatable $relatable): string => "{$relatable->article->title} -> {$relatable->relatable?->getAttribute('title')}")
            ->all();
        $seo_titles = Seo::query()->with('seoable')->get()
            ->mapWithKeys(fn (Seo $seo): array => [(string) $seo->title => $seo->seoable?->getAttribute('title')])
            ->all();
        $lead = Lead::query()->with('leadable')->sole();

        expect($relatables)->toEqualCanonicalizing(['Article two -> Project four', 'Article two -> Article four', 'Article four -> Project two'])
            ->and($seo_titles)->toEqualCanonicalizing(['Project two SEO' => 'Project two', 'Article two SEO' => 'Article two'])
            ->and($lead->leadable->email)->toBe('lead@example.test')
            ->and(Project::query()->where('slug->en', 'project-two')->sole()->tags->pluck('name')->all())->toBe(['Laravel'])
            ->and(BlogArticle::query()->where('slug->en', 'article-two')->sole()->tags->pluck('name')->all())->toBe(['Laravel'])
            ->and(Campaign::query()->sole()->tags->pluck('name')->all())->toBe(['Laravel'])
            ->and(Project::query()->where('slug->en', 'project-two')->sole()->getMedia('featured_image')->pluck('file_name')->all())->toBe(['cover.jpg'])
            ->and(Client::query()->where('name', 'Globex')->sole()->getMedia('client_image')->pluck('file_name')->all())->toBe(['logo.png']);
    });

    it('keeps the stored path of media uploaded before the conversion', function () {
        $media = Media::query()->where('file_name', 'cover.jpg')->sole();

        expect($media->getAttribute('legacy_model_id'))->toBe(2)
            ->and((new MediaPathGenerator)->getPath($media))->toBe('projects/2/featured_image/');
    });

    it('remaps the activity log subjects, causers and logged ids', function () {
        $created = Activity::query()->where('event', 'created')->sole();
        $deleted = Activity::query()->where('event', 'deleted')->sole();
        $updated = Activity::query()->where('event', 'updated')->sole();
        $article = BlogArticle::query()->where('slug->en', 'article-four')->sole();
        $alice = User::query()->where('email', 'alice@example.test')->sole();
        $contact_lead = ContactLead::query()->sole();

        expect($created->subject?->is($article))->toBeTrue()
            ->and($created->causer?->is($alice))->toBeTrue()
            ->and($created->attribute_changes?->get('attributes'))->toBe(['id' => $article->id, 'author_id' => $alice->id])
            ->and($updated->subject?->is(Lead::query()->sole()))->toBeTrue()
            ->and($updated->attribute_changes?->get('attributes')['leadable_id'] ?? null)->toBe($contact_lead->id)
            ->and($deleted->subject_type)->toBe(Project::class)
            ->and($deleted->subject_id)->toBeNull()
            ->and($deleted->causer?->getAttribute('email'))->toBe('bob@example.test')
            ->and($deleted->attribute_changes?->get('old'))->toBe(['id' => 999, 'client_id' => Client::query()->where('name', 'Acme')->sole()->id]);
    });

    it('converts the seo settings and users', function () {
        expect(SeoSetting::query()->sole()->title_separator)->toBe('|')
            ->and(User::query()->orderBy('id')->pluck('email')->all())->toBe(['alice@example.test', 'bob@example.test']);
    });
});

describe('orphan references', function () {
    it('aborts without changes when a required reference points to a missing record', function () {
        DB::table('seo')->insert(['id' => 3, 'seoable_type' => Project::class, 'seoable_id' => 404]);

        expect(fn () => Artisan::call('migrate', ['--force' => true]))
            ->toThrow(RuntimeException::class, 'Cannot convert seo.seoable_id to uuid: 1 rows reference a missing record.')
            ->and(Schema::getColumnType('users', 'id'))->toBe('int8')
            ->and(Schema::getColumnType('seo', 'seoable_id'))->toBe('int8')
            ->and(DB::table('sessions')->count())->toBe(2);
    });

    it('aborts when a required morph reference has a type that is not an app model', function () {
        DB::table('taggables')->insert(['tag_id' => 1, 'taggable_type' => 'App\Models\Removed', 'taggable_id' => 1]);

        expect(fn () => Artisan::call('migrate', ['--force' => true]))
            ->toThrow(RuntimeException::class, 'Cannot convert taggables.taggable_id to uuid: 1 rows reference a missing record.');
    });

    it('empties a nullable reference that points to a missing record', function () {
        expect(DB::table('activity_log')->where('event', 'deleted')->value('subject_id'))->toBe(999);

        Artisan::call('migrate', ['--force' => true]);

        expect(DB::table('activity_log')->where('event', 'deleted')->value('subject_id'))->toBeNull();
    });
});
