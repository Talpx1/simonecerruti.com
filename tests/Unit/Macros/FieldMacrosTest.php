<?php

declare(strict_types=1);

use Filament\Forms\Components\TextInput;

it('registers field text-transform macros that set an onInput handler', function (string $macro, string $expected) {
    $field = TextInput::make('test')->{$macro}();

    expect($field->getExtraInputAttributes())->toHaveKey('onInput')
        ->and($field->getExtraInputAttributes()['onInput'])->toContain($expected);
})->with([
    'uppercase' => ['uppercase', 'toUpperCase'],
    'lowercase' => ['lowercase', 'toLowerCase'],
    'capitalizeWords' => ['capitalizeWords', 'toUpperCase'],
    'capitalizeFirstChar' => ['capitalizeFirstChar', 'toUpperCase'],
]);
