<?php

declare(strict_types=1);

namespace App\Filament\Macros\Field;

use App\Macros\Concerns\Macro;

class CapitalizeFirstCharMacro implements Macro {
    public static function register(): array {
        return [
            'capitalizeFirstChar',
            fn () => $this->extraInputAttributes(['onInput' => 'this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1)']),
        ];
    }
}
