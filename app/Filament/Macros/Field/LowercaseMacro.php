<?php

declare(strict_types=1);

namespace App\Filament\Macros\Field;

use App\Macros\Concerns\Macro;

class LowercaseMacro implements Macro {
    public static function register(): array {
        return [
            'lowercase',
            fn () => $this
                ->autocapitalize('off')
                ->extraInputAttributes(['onInput' => 'this.value = this.value.toLowerCase()']),
        ];
    }
}
