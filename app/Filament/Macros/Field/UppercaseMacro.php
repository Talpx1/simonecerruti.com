<?php

declare(strict_types=1);

namespace App\Filament\Macros\Field;

use App\Macros\Concerns\Macro;

class UppercaseMacro implements Macro {
    public static function register(): array {
        return [
            'uppercase',
            fn () => $this
                ->autocapitalize('characters')
                ->extraInputAttributes(['onInput' => 'this.value = this.value.toUpperCase()']),
        ];
    }
}
