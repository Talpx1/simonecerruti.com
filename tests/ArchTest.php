<?php

declare(strict_types=1);

use Illuminate\Support\Str;

use function Safe\glob;

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
    ->ignoring(['App\Enums\Concerns', 'App\Enums\Contracts']);

arch('enum concerns are traits')
    ->expect('App\Enums\Concerns')
    ->toBeTraits();

arch('enum contracts are interfaces')
    ->expect('App\Enums\Contracts')
    ->toBeInterfaces();

arch('status-like enums are string backed')
    ->expect(['App\Enums\BlogArticleStatuses', 'App\Enums\LeadReadStatus', 'App\Enums\TagTypes'])
    ->toBeStringBackedEnums();

arch('the blog category enum is integer backed')
    ->expect('App\Enums\BlogCategories')
    ->toBeIntBackedEnums();

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

arch('macros extend the Macro contract')
    ->expect('App\Macros')
    ->classes()
    ->toExtend('App\Macros\Contracts\Macro')
    ->ignoring('App\Macros\Contracts');

arch('macros are final')
    ->expect('App\Macros')
    ->classes()
    ->toBeFinal()
    ->ignoring('App\Macros\Contracts');

arch('exceptions are throwable')
    ->expect('App\Exceptions')
    ->toImplement('Throwable');

arch('exceptions are not final')
    ->expect('App\Exceptions')
    ->classes()
    ->not->toBeFinal();

test('every exception extends the root exception of its own domain and no other root', function (): void {
    $exception_classes = collect(glob(app_path('Exceptions/*.php')))
        ->map(fn (string $file): string => 'App\\Exceptions\\'.pathinfo($file, PATHINFO_FILENAME))
        ->filter(fn (string $class): bool => class_exists($class))
        ->values();

    $root_classes = $exception_classes
        ->reject(fn (string $class): bool => str_starts_with((string) get_parent_class($class), 'App\\Exceptions\\'))
        ->values();

    $domain_of = fn (string $class): string => Str::beforeLast(class_basename($class), 'Exception');

    $violations = $exception_classes
        ->flatMap(function (string $class) use ($root_classes, $domain_of): array {
            if (! str_ends_with($class, 'Exception')) {
                return ["{$class} must be named <Domain>Exception or <Domain><Case>Exception."];
            }

            if ($root_classes->contains($class)) {
                return is_subclass_of($class, RuntimeException::class) ? [] : ["{$class} is a root exception and must extend RuntimeException."];
            }

            $domain_root = $root_classes
                ->filter(fn (string $root): bool => str_starts_with(class_basename($class), $domain_of($root)))
                ->sortByDesc(fn (string $root): int => mb_strlen($domain_of($root)))
                ->first();

            if ($domain_root === null) {
                return ["{$class} is not named after the domain of any root exception."];
            }

            $ancestor_roots = $root_classes->filter(fn (string $root): bool => is_subclass_of($class, $root))->values()->all();

            return $ancestor_roots === [$domain_root] ? [] : ["{$class} must extend {$domain_root} and no other root exception."];
        })
        ->values()
        ->all();

    expect($violations)->toBeEmpty();
});
