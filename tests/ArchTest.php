<?php

declare(strict_types=1);

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
