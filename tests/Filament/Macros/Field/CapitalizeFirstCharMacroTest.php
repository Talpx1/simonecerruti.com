<?php

declare(strict_types=1);

use Filament\Forms\Components\TextInput;

describe('capitalizeFirstChar', function (): void {
    it('sets an onInput handler that capitalizes the first character', function (): void {
        $field = TextInput::make('test');

        expect($field->getExtraInputAttributes())->not->toHaveKey('onInput');

        $field->capitalizeFirstChar();

        expect($field->getExtraInputAttributes()['onInput'])->toContain('charAt(0).toUpperCase()');
    });
});
