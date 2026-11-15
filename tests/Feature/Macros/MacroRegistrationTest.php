<?php

declare(strict_types=1);

use Filament\Forms\Components\TextInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

it('registers the macro on its macroable class', function (string $macroable, string $name) {
    expect($macroable::hasMacro($name))->toBeTrue();
})->with([
    'Str::replacePlaceholders' => [Str::class, 'replacePlaceholders'],
    'Request::queryStringOrNull' => [Request::class, 'queryStringOrNull'],
    'Request::queryStringOrDefault' => [Request::class, 'queryStringOrDefault'],
    'Request::cookieStringOrDefault' => [Request::class, 'cookieStringOrDefault'],
    'TextInput::uppercase' => [TextInput::class, 'uppercase'],
    'TextInput::lowercase' => [TextInput::class, 'lowercase'],
    'TextInput::capitalizeWords' => [TextInput::class, 'capitalizeWords'],
    'TextInput::capitalizeFirstChar' => [TextInput::class, 'capitalizeFirstChar'],
    'App::supportedLocales' => [App::class, 'supportedLocales'],
    'App::supportedLocaleCodes' => [App::class, 'supportedLocaleCodes'],
    'Route::localizedUrl' => [Route::class, 'localizedUrl'],
    'Route::localizedUrlString' => [Route::class, 'localizedUrlString'],
    'Route::livewireLocalized' => [Route::class, 'livewireLocalized'],
]);
