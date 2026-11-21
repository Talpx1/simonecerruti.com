<?php

declare(strict_types=1);

use App\Actions\DetectVisitSource;
use App\Data\VisitSourceData;
use App\Enums\ServiceAreas;
use App\Enums\TagTypes;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use App\Settings\BlogSettings;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Livewire\Features\SupportTesting\Testable;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\withoutMiddleware;
use function Pest\Livewire\livewire;
use function Safe\file_put_contents;
use function Safe\mkdir;
use function Safe\putenv;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in(__DIR__)
    ->beforeEach(function (): void {
        $this->withoutVite();
        Mail::fake();
        Notification::fake();
        Sleep::fake();
        Storage::fake('local');
        Storage::fake('public');
        App::setLocale(config()->string('app.locale'));
        Http::preventStrayRequests();
        Process::fake([]);
        Process::preventStrayProcesses();
        $this->freezeTime();
    })
    ->afterEach(function (): void {
        Str::createUuidsNormally();
    });

pest()->in('Browser')
    ->beforeAll(function (): void {
        $manifest = dirname(__DIR__).'/public/build/manifest.json';
        $newest_source = 0;

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__DIR__).'/resources', FilesystemIterator::SKIP_DOTS)) as $source) {
            $newest_source = max($newest_source, $source->getMTime());
        }

        $lock = fopen(dirname(__DIR__).'/storage/framework/vite-build.lock', 'c');

        flock($lock, LOCK_EX);

        if (! is_file($manifest) || filemtime($manifest) < $newest_source) {
            exec('npm run build 2>&1', $output, $exit_code);

            throw_if($exit_code !== 0, RuntimeException::class, implode("\n", $output));
        }

        flock($lock, LOCK_UN);
        fclose($lock);
    })
    ->beforeEach(function (): void {
        Vite::useHotFile(storage_path('framework/vite.hot'));
    });

expect()->intercept(
    'toContain',
    fn (mixed $value): bool => is_a($value, Collection::class) && $value->every(fn (mixed $entry): bool => is_a($entry, Model::class)),
    fn (Collection $models) => expect($this->value->pluck('id'))->toContain(...$models->pluck('id')->toArray())
);

// expect the provided model to be the same model
expect()->intercept('toBe', Model::class, function (Model $expected): void {
    expect($expected->is($this->value))->toBeTrue(
        'Failed asserting that two models are the same record (same table, connection and key).'
    );
});

expect()->intercept('toEqual', Model::class, function (Model $expected): void {
    expect($expected->is($this->value))->toBeTrue(
        'Failed asserting that two models are the same record (same table, connection and key).'
    );
});

/**
 * Authenticate as a user allowed to access the Filament admin panel.
 */
function authenticateAsAdmin(): User {
    $user = User::factory()->admin()->create();

    test()->actingAs($user);

    return $user;
}

/**
 * Run the given callback with the application locale temporarily switched.
 *
 * @template TReturn
 *
 * @param  Closure(): TReturn  $callback
 * @return TReturn
 */
function runWithLocale(string $locale, Closure $callback): mixed {
    $original = app()->getLocale();

    app()->setLocale($locale);

    try {
        return $callback();
    } finally {
        app()->setLocale($original);
    }
}

/**
 * @return EloquentCollection<int, Activity>
 */
function getActivityLogsFor(Model $subject, ?string $event = null): EloquentCollection {
    return Activity::query()
        ->forSubject($subject)
        ->when($event !== null, fn (Builder $query): Builder => $query->forEvent($event))
        ->orderBy('id')
        ->get();
}

function getLastActivityLogsFor(Model $subject, ?string $event = null): ?Activity {
    return getActivityLogsFor($subject, $event)->last();
}

function assertNoActivityLogsFor(Model $subject, ?string $event = null): void {
    expect(getActivityLogsFor($subject, $event))->toBeEmpty();
}

function assertCountActivityLogsFor(Model $subject, int $count, ?string $event = null): void {
    expect(getActivityLogsFor($subject, $event))->toHaveCount($count);
}

function createServiceTag(ServiceAreas $area, string $name): Tag {
    $tag = Tag::findOrCreate($name, TagTypes::TAG->value);
    $tag->update(['service' => $area]);

    return $tag;
}

/**
 * @return array{0: Project, 1: Project, 2: Project}
 */
function createRankedProjects(): array {
    return [
        Project::factory()->featured()->create(['created_at' => now()->subDays(5)]),
        Project::factory()->create(['created_at' => now()->subDay()]),
        Project::factory()->create(['created_at' => now()->subDays(10)]),
    ];
}

function detectVisitSource(
    ?string $utm_source = null,
    ?string $utm_medium = null,
    ?string $utm_campaign = null,
    ?string $utm_term = null,
    ?string $utm_content = null,
    ?string $referrer_url = null,
    string $current_host = 'example.test',
): VisitSourceData {
    return DetectVisitSource::run(
        utm_source: $utm_source,
        utm_medium: $utm_medium,
        utm_campaign: $utm_campaign,
        utm_term: $utm_term,
        utm_content: $utm_content,
        referrer_url: $referrer_url,
        current_host: $current_host,
    );
}

function createAiHooksProject(bool $has_hooks_folder = true): string {
    $project = \sys_get_temp_dir().'/ai_hooks_'.\uniqid();

    if ($has_hooks_folder) {
        mkdir("{$project}/.ai/hooks", 0755, true);
        file_put_contents("{$project}/.ai/hooks/guard.sh", '#!/usr/bin/env bash');

        return $project;
    }

    mkdir($project, 0755, true);

    return $project;
}

function switchToUnreachableDatabaseConnection(): void {
    config([
        'database.connections.unreachable' => [
            ...config()->array('database.connections.pgsql'),
            'host' => '127.0.0.1',
            'port' => 1,
        ],
        'database.default' => 'unreachable',
    ]);
}

function actingAsNonAdminUser(): User {
    $user = User::factory()->create();

    actingAs($user);

    return $user;
}

function assertPanelUrlIsForbiddenForNonAdminUser(string $url): void {
    actingAsNonAdminUser();

    get($url)->assertForbidden();
}

function assertPanelUrlRedirectsGuestToLogin(string $url): void {
    Auth::logout();

    get($url)->assertRedirect(Filament::getLoginUrl());
}

/**
 * @return array{datasets: array<int, array<string, mixed>>, labels: array<int, string>}
 */
function getRenderedChartWidgetData(Testable $widget): array {
    preg_match("/cachedData: JSON\\.parse\\('(.*)'\\)/", $widget->html(), $matches);

    /** @var array{datasets: array<int, array<string, mixed>>, labels: array<int, string>} */
    return json_decode(str_replace(['\\u0022', '\\/', '\\\\'], ['"', '/', '\\'], $matches[1]), true, flags: JSON_THROW_ON_ERROR);
}

/**
 * @param  array<int, array{image: string, caption: string|null}>  $images
 * @return array<string, mixed>
 */
function buildRichContentWithGalleryBlock(array $images): array {
    return [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'customBlock',
                'attrs' => [
                    'id' => 'gallery',
                    'config' => ['images' => $images],
                ],
            ],
        ],
    ];
}

/**
 * @return array<string, string>
 */
function getRenderedStatsOverviewValues(Testable $widget): array {
    preg_match_all(
        '/fi-wi-stats-overview-stat-label">\s*(.*?)\s*<\/span>.*?fi-wi-stats-overview-stat-value">\s*(.*?)\s*<\/div>/s',
        $widget->html(),
        $matches,
    );

    return \array_combine(
        \array_map(html_entity_decode(...), $matches[1]),
        \array_map(html_entity_decode(...), $matches[2]),
    );
}

/**
 * @param  array<string, mixed>  $attributes
 */
function createTag(array $attributes = []): Tag {
    return Tag::query()->create([
        'name' => ['it' => 'Laravel', 'en' => 'Laravel'],
        'type' => TagTypes::TAG->value,
        ...$attributes,
    ]);
}

function insertOrphanTag(int $id, string $type): void {
    Tag::query()->insert([
        'id' => $id,
        'name' => \json_encode(['en' => 'Orphan']),
        'slug' => \json_encode(['en' => 'orphan']),
        'type' => $type,
    ]);
}

function getUrlWithoutLocaleRedirects(string $url): TestResponse {
    withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
    ]);

    return get($url);
}

/**
 * @param  array<string, mixed>  $parameters
 */
function getPageWithoutLocaleRedirects(string $route_name, array $parameters = []): TestResponse {
    return getUrlWithoutLocaleRedirects(route($route_name, $parameters));
}

function getBrowserUserAgent(): string {
    return 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
}

/**
 * @param  array<string, string>  $overrides
 * @return array<string, string>
 */
function getBrowserRequestHeaders(array $overrides = []): array {
    return [
        'User-Agent' => getBrowserUserAgent(),
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language' => 'it-IT,it;q=0.9,en;q=0.8',
        'Accept-Encoding' => 'gzip, deflate, br',
        'Sec-Fetch-Site' => 'none',
        ...$overrides,
    ];
}

/**
 * @return array<string, string>
 */
function getAutomatedRequestHeaders(): array {
    return [
        'User-Agent' => getBrowserUserAgent(),
        'Accept' => '',
        'Accept-Language' => '',
    ];
}

function getAnalyticsConsentCookieValue(): string {
    return \Safe\json_encode(['version' => 1, 'analytics' => true]);
}

/**
 * @param  array<string, string>  $author_bio
 */
function updateBlogAuthorBio(array $author_bio): void {
    $settings = resolve(BlogSettings::class);
    $settings->author_bio = $author_bio;
    $settings->save();
}

/**
 * @return array<string, mixed>
 */
function getValidContactFormData(): array {
    return [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'company_name' => 'Acme',
        'email' => 'john@example.com',
        'phone' => '+39 333 1234567',
        'message' => 'I would like to discuss a project with you.',
        'acceptance' => true,
    ];
}

function fakeRecaptchaResponseInProduction(float $score): void {
    app()->detectEnvironment(fn (): string => 'production');

    Http::fake([
        config()->string('services.recaptcha.verify_url') => Http::response([
            'success' => true,
            'action' => 'contact_form',
            'score' => $score,
        ]),
    ]);
}

function assertPageEmitsMetaDescription(string $route_name, string $description): void {
    getPageWithoutLocaleRedirects($route_name)
        ->assertOk()
        ->assertSee('<meta name="description" content="'.e($description).'">', escape: false);
}

function assertPageIsIndexable(string $route_name): void {
    getPageWithoutLocaleRedirects($route_name)
        ->assertOk()
        ->assertDontSee('name="robots"', escape: false);
}

function assertPublicPageRendersFullSeoHead(string $url, string $expected_title, string $schema_type): void {
    $html = (string) getUrlWithoutLocaleRedirects($url)->assertOk()->getContent();

    expect($html)
        ->toContain('<title>'.$expected_title.' | '.config('app.name').'</title>')
        ->toContain('<meta name="description"')
        ->toContain('<link rel="canonical"')
        ->toContain('property="og:title"')
        ->toContain('name="twitter:card"')
        ->toContain('"@type":"'.$schema_type.'"')
        ->toContain('"@type":"WebSite"')
        ->and(\substr_count($html, '<title'))->toBe(1)
        ->and(\substr_count($html, 'application/ld+json'))->toBe(1);
}

/**
 * @param  class-string  $component
 */
function assertServiceLandingHidesShowcaseWithoutTaggedProjects(string $component, ServiceAreas $area): void {
    $other_area = collect(ServiceAreas::cases())->first(fn (ServiceAreas $candidate): bool => $candidate !== $area);
    $other_area_project = Project::factory()->published()->create();

    livewire($component)->assertOk()->assertDontSee($other_area_project->title);

    $other_area_project->attachTag(createServiceTag($other_area, 'Other area'));

    livewire($component)
        ->assertOk()
        ->assertDontSee(__('Already<br>in use'), escape: false)
        ->assertDontSee($other_area_project->title);
}

/**
 * @param  class-string  $component
 */
function assertServiceLandingShowcasesTaggedProjects(string $component, ServiceAreas $area, string $tag_name): Testable {
    $tag = createServiceTag($area, $tag_name);
    $project = Project::factory()->published()->create();
    $unpublished = Project::factory()->unpublished()->create();

    livewire($component)
        ->assertOk()
        ->assertDontSee($project->title)
        ->assertDontSee($unpublished->title);

    $project->attachTag($tag);
    $unpublished->attachTag($tag);

    return livewire($component)
        ->assertOk()
        ->assertSee(__('Already<br>in use'), escape: false)
        ->assertSee($project->title)
        ->assertDontSee($unpublished->title)
        ->assertSeeHtml(route('project.show', $project->slug));
}

/**
 * @param  class-string  $component
 */
function assertLegalPageRendersNavigation(string $component, string $current_label): void {
    livewire($component)
        ->assertOk()
        ->assertSeeHtml('class="legal-h1"')
        ->assertSeeHtml('data-effect="legal"')
        ->assertSeeHtml('class="article-toc"')
        ->assertSeeHtml('class="article-progress"')
        ->assertSeeHtml('class="is-current" aria-current="page">'.__($current_label).'</a>')
        ->assertSeeHtml('href="'.route('privacy_policy').'"')
        ->assertSeeHtml('href="'.route('cookie_policy').'"')
        ->assertSeeHtml('href="'.route('terms_and_conditions').'"');
}

/**
 * @param  class-string  $component
 */
function assertLegalPageAnchorsEverySideIndexEntry(string $component): void {
    $html = livewire($component)->html();

    \Safe\preg_match_all('/<a href="#([a-z0-9-]+)">/', $html, $anchors);

    $orphan_anchors = \array_filter(
        $anchors[1],
        fn (string $anchor): bool => ! \str_contains($html, '<h2 id="'.$anchor.'">'),
    );

    expect($anchors[1])->not->toBeEmpty()
        ->and($orphan_anchors)->toBeEmpty();
}

/**
 * @param  class-string  $component
 */
function assertLegalPageReadsContactEmailFromConfig(string $component): void {
    config()->set('company.contacts.email', 'legal@example.test');

    livewire($component)->assertSeeHtml('mailto:legal@example.test');
}

function loadRoutesForLocale(string $locale): void {
    putenv("ROUTING_LOCALE={$locale}");

    Route::setRoutes(new RouteCollection);
    Route::middleware('web')->group(base_path('routes/web.php'));
    Route::getRoutes()->refreshNameLookups();
    Route::getRoutes()->refreshActionLookups();
}

/**
 * @return list<string>
 */
function getLocalizedPublicRouteNames(): array {
    return collect(Route::getRoutes()->getRoutes())
        ->filter(fn (RoutingRoute $route): bool => \in_array('GET', $route->methods(), true)
            && $route->getName() !== null
            && $route->parameterNames() === []
            && \in_array('localize', $route->gatherMiddleware(), true))
        ->map(fn (RoutingRoute $route): string => (string) $route->getName())
        ->values()
        ->all();
}
