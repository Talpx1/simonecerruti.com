<?php

declare(strict_types=1);

use Filament\Forms\Components\TextInput;

describe('capitalizeWords', function (): void {
    it('sets an onInput handler that capitalizes every word', function (): void {
        $field = TextInput::make('test');

        expect($field->getExtraInputAttributes())->not->toHaveKey('onInput');

        $field->capitalizeWords();

        expect($field->getExtraInputAttributes()['onInput'])->toContain('toUpperCase()')
            ->and($field->getAutocapitalize())->toBe('words');
    });
});
