<?php

declare(strict_types=1);

use Filament\Forms\Components\TextInput;

describe('lowercase', function (): void {
    it('sets an onInput handler that lowercases the value', function (): void {
        $field = TextInput::make('test');

        expect($field->getExtraInputAttributes())->not->toHaveKey('onInput');

        $field->lowercase();

        expect($field->getExtraInputAttributes()['onInput'])->toContain('toLowerCase()')
            ->and($field->getAutocapitalize())->toBe('off');
    });
});
