<?php

declare(strict_types=1);

use Filament\Forms\Components\TextInput;

describe('uppercase', function (): void {
    it('sets an onInput handler that uppercases the value', function (): void {
        $field = TextInput::make('test');

        expect($field->getExtraInputAttributes())->not->toHaveKey('onInput');

        $field->uppercase();

        expect($field->getExtraInputAttributes()['onInput'])->toContain('toUpperCase()')
            ->and($field->getAutocapitalize())->toBe('characters');
    });
});
