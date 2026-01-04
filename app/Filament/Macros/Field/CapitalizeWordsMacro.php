<?php

declare(strict_types=1);

namespace App\Filament\Macros\Field;

use App\Macros\Concerns\Macro;

class CapitalizeWordsMacro implements Macro {
    public static function register(): array {
        return [
            'capitalizeWords',
            fn () => $this
                ->autocapitalize('words')
                ->extraInputAttributes(['onInput' => 'this.value = this.value.toLowerCase().replace(/\b\w/g, l => l.toUpperCase())']),
        ];
    }
}
