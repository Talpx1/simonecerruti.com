<?php

declare(strict_types=1);

namespace App\Providers;

use App\Macros\TextInput\CapitalizeFirstCharMacro;
use App\Macros\TextInput\CapitalizeWordsMacro;
use App\Macros\TextInput\LowercaseMacro;
use App\Macros\TextInput\UppercaseMacro;
use Illuminate\Support\ServiceProvider;

final class TextInputMacroServiceProvider extends ServiceProvider {
    public function register(): void {
        UppercaseMacro::register();
        LowercaseMacro::register();
        CapitalizeWordsMacro::register();
        CapitalizeFirstCharMacro::register();
    }
}
