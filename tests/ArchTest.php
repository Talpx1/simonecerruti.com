<?php

declare(strict_types=1);

use App\Models\Concerns\LogsAllDirtyChanges;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

arch()->preset()->php();

arch('strict types everywhere')
    ->expect('App')
    ->toUseStrictTypes();

arch('no debugging helpers are left behind')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'print_r'])
    ->each->not->toBeUsed();

arch('configuration is read through config(), never env()')
    ->expect('env')
    ->not->toBeUsed();

/*
|--------------------------------------------------------------------------
| Models
|--------------------------------------------------------------------------
*/

arch('models extend the Eloquent base model')
    ->expect('App\Models')
    ->classes()
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->ignoring('App\Models\User');

arch('the user model is authenticatable')
    ->expect('App\Models\User')
    ->toExtend('Illuminate\Foundation\Auth\User');

arch('model concerns are traits')
    ->expect('App\Models\Concerns')
    ->toBeTraits();

/*
|--------------------------------------------------------------------------
| Enums
|--------------------------------------------------------------------------
*/

arch('enums are enums')
    ->expect('App\Enums')
    ->toBeEnums()
    ->ignoring('App\Enums\Concerns');

arch('enum concerns are traits')
    ->expect('App\Enums\Concerns')
    ->toBeTraits();

arch('status-like enums are string backed')
    ->expect(['App\Enums\BlogArticleStatuses', 'App\Enums\LeadReadStatus', 'App\Enums\TagTypes'])
    ->toBeStringBackedEnums();

/*
|--------------------------------------------------------------------------
| Data
|--------------------------------------------------------------------------
*/

arch('data transfer objects extend the Spatie data class')
    ->expect('App\Data')
    ->classes()
    ->toExtend('Spatie\LaravelData\Data');

/*
|--------------------------------------------------------------------------
| Livewire
|--------------------------------------------------------------------------
*/

arch('livewire classes extend the base component')
    ->expect('App\Livewire')
    ->classes()
    ->toExtend('Livewire\Component')
    ->ignoring('App\Livewire\Concerns');

arch('livewire concerns are traits')
    ->expect('App\Livewire\Concerns')
    ->toBeTraits();

/*
|--------------------------------------------------------------------------
| Filament, Mail, Console & Macros
|--------------------------------------------------------------------------
*/

arch('filament resources extend the base resource')
    ->expect([
        'App\Filament\Resources\BlogArticles\BlogArticleResource',
        'App\Filament\Resources\Projects\ProjectResource',
        'App\Filament\Resources\Tags\TagResource',
        'App\Filament\Resources\Leads\LeadResource',
        'App\Filament\Resources\Clients\ClientResource',
    ])
    ->toExtend('Filament\Resources\Resource');

arch('mailables extend the base mailable')
    ->expect('App\Mail')
    ->toExtend('Illuminate\Mail\Mailable');

arch('console commands extend the base command')
    ->expect('App\Console\Commands')
    ->toExtend('Illuminate\Console\Command');

arch('field macros implement the macro contract')
    ->expect('App\Filament\Macros\Field')
    ->toImplement('App\Macros\Concerns\Macro');

arch('string macros implement the macro contract')
    ->expect('App\Macros\Str')
    ->toImplement('App\Macros\Concerns\Macro');

arch('exceptions are throwable')
    ->expect('App\Exceptions')
    ->toImplement('Throwable');

/*
|--------------------------------------------------------------------------
| Project-wide standards
|--------------------------------------------------------------------------
*/

arch('security')
    ->preset()->security()
    ->ignoring(['App\Macros\Str\ReplacePlaceholdersMacro', 'App\Filament\Components\FormDataProcessingAcceptance', 'App\Enums\Concerns\HasRandomPicker']);

arch('models are final')
    ->expect('App\Models')
    ->classes()
    ->toBeFinal()
    ->ignoring('App\Models\Tag');

arch('policies are final')
    ->expect('App\Policies')
    ->classes()
    ->toBeFinal();

arch('models use uuid primary keys')
    ->expect('App\Models')
    ->classes()
    ->toUseTrait(HasUuids::class)
    ->ignoring('App\Models\Tag');

arch('models have a factory')
    ->expect('App\Models')
    ->classes()
    ->toUseTrait(HasFactory::class)
    ->ignoring('App\Models\Tag');

arch('models do not depend on the HTTP, console or Filament layers')
    ->expect('App\Models')
    ->not->toUse(['App\Http', 'App\Console', 'App\Filament']);

arch('actions do not depend on the HTTP layer or the authenticated user')
    ->expect('App\Actions')
    ->not->toUse(['App\Http', 'Illuminate\Http', Auth::class, 'request', 'auth', 'session']);

arch('dates are immutable')
    ->expect('App')
    ->not->toUse([Carbon::class, Illuminate\Support\Carbon::class]);

test('only the user, tag and analytics models skip the activity log', function (): void {
    $exempt = ['User', 'Tag', 'PageView', 'VisitSession', 'BlogArticleRelatable', 'Campaign'];

    $violations = collect(glob(app_path('Models/*.php')))
        ->map(fn (string $file): string => pathinfo($file, PATHINFO_FILENAME))
        ->reject(fn (string $name): bool => \in_array($name, $exempt, true))
        ->map(fn (string $name): string => 'App\\Models\\'.$name)
        ->filter(fn (string $class): bool => class_exists($class))
        ->reject(fn (string $class): bool => \in_array(LogsAllDirtyChanges::class, class_uses_recursive($class), true))
        ->values()
        ->all();

    expect($violations)->toBeEmpty();
});

test('Actions live directly in app/Actions, without subfolders', function (): void {
    $subfolders = collect(glob(app_path('Actions/*'), GLOB_ONLYDIR) ?: [])
        ->map(fn (string $directory): string => basename($directory))
        ->all();

    expect($subfolders)->toBeEmpty();
});

test('every Action in app/Actions is final and exposes exactly one public handle method', function (): void {
    $violations = collect(glob(app_path('Actions/*.php')))
        ->map(fn (string $file): string => 'App\\Actions\\'.pathinfo($file, PATHINFO_FILENAME))
        ->filter(fn (string $class): bool => class_exists($class))
        ->flatMap(function (string $action_class): array {
            $reflection = new ReflectionClass($action_class);

            $trait_method_names = collect(class_uses_recursive($action_class))
                ->flatMap(fn (string $trait): array => array_map(
                    fn (ReflectionMethod $method): string => $method->getName(),
                    new ReflectionClass($trait)->getMethods(ReflectionMethod::IS_PUBLIC),
                ))
                ->unique()
                ->values()
                ->all();

            $own_public_method_names = collect($reflection->getMethods(ReflectionMethod::IS_PUBLIC))
                ->map(fn (ReflectionMethod $method): string => $method->getName())
                ->reject(fn (string $name): bool => \in_array($name, $trait_method_names, true))
                ->reject(fn (string $name): bool => $name === '__construct')
                ->values()
                ->all();

            return array_filter([
                $reflection->isFinal() ? null : "{$action_class} must be final.",
                $own_public_method_names === ['handle'] ? null : "{$action_class} must expose exactly one public handle() method, found: ".implode(', ', $own_public_method_names),
            ]);
        })
        ->values()
        ->all();

    expect($violations)->toBeEmpty();
});

test('Concerns folders hold only traits and Contracts folders only interfaces or abstract classes', function (): void {
    $violations = collect(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(app_path(), FilesystemIterator::SKIP_DOTS)))
        ->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php')
        ->filter(fn (SplFileInfo $file): bool => \in_array(basename($file->getPath()), ['Concerns', 'Contracts'], true))
        ->reject(fn (SplFileInfo $file): bool => str_contains($file->getPathname(), 'app/Macros/'))
        ->map(fn (SplFileInfo $file): string => 'App\\'.str_replace('/', '\\', mb_substr($file->getPathname(), mb_strlen(app_path()) + 1, -4)))
        ->reject(function (string $class): bool {
            if (str_contains($class, '\\Concerns\\')) {
                return trait_exists($class);
            }

            return interface_exists($class) || (class_exists($class) && new ReflectionClass($class)->isAbstract());
        })
        ->values()
        ->all();

    expect($violations)->toBeEmpty();
});

test('models never opt out of mass-assignment protection', function (): void {
    $violations = collect(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(app_path(), FilesystemIterator::SKIP_DOTS)))
        ->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php')
        ->filter(fn (SplFileInfo $file): bool => preg_match('/\bunguard(ed)?\s*\(|\$guarded\b/', (string) file_get_contents($file->getPathname())) === 1)
        ->map(fn (SplFileInfo $file): string => $file->getPathname())
        ->values()
        ->all();

    expect($violations)->toBeEmpty();
});

test('migrations added after the UUID conversion are forward-only and use UUID keys', function (): void {
    $uuid_conversion = '2026_11_16_110000';

    $violations = collect(glob(database_path('migrations/*.php')))
        ->filter(fn (string $file): bool => basename($file) > $uuid_conversion)
        ->flatMap(function (string $file): array {
            $contents = file_get_contents($file);

            return array_filter([
                preg_match('/function\s+down\s*\(/', $contents) === 1 ? basename($file).' must not define down().' : null,
                preg_match('/->foreignId\s*\(/', $contents) === 1 ? basename($file).' must use foreignUuid() instead of foreignId().' : null,
                preg_match('/\$table->id\s*\(/', $contents) === 1 ? basename($file).' must use uuid(\'id\')->primary() instead of id().' : null,
            ]);
        })
        ->values()
        ->all();

    expect($violations)->toBeEmpty();
});

test('every test file declares strict types', function (): void {
    $violations = collect(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path('tests'), FilesystemIterator::SKIP_DOTS)))
        ->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php')
        ->filter(fn (SplFileInfo $file): bool => ! str_contains((string) file_get_contents($file->getPathname()), 'declare(strict_types=1);'))
        ->map(fn (SplFileInfo $file): string => mb_substr($file->getPathname(), mb_strlen(base_path()) + 1))
        ->values()
        ->all();

    expect($violations)->toBeEmpty();
});
