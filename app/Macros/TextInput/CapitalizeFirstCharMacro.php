<?php

declare(strict_types=1);

namespace App\Macros\TextInput;

use App\Macros\Contracts\InstanceMacro;
use Filament\Forms\Components\TextInput;

final class CapitalizeFirstCharMacro extends InstanceMacro {
    protected static function macroable(): string {
        return TextInput::class;
    }

    protected static function name(): string {
        return 'capitalizeFirstChar';
    }

    public static function handle(TextInput $field): TextInput {
        return $field->extraInputAttributes(['onInput' => 'this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1)']);
    }
}
