<?php

declare(strict_types=1);

namespace App\Macros\TextInput;

use App\Macros\Contracts\InstanceMacro;
use Filament\Forms\Components\TextInput;

final class CapitalizeWordsMacro extends InstanceMacro {
    protected static function macroable(): string {
        return TextInput::class;
    }

    protected static function name(): string {
        return 'capitalizeWords';
    }

    public static function handle(TextInput $field): TextInput {
        return $field
            ->autocapitalize('words')
            ->extraInputAttributes(['onInput' => 'this.value = this.value.toLowerCase().replace(/\b\w/g, l => l.toUpperCase())']);
    }
}
