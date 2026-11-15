<?php

declare(strict_types=1);

namespace App\Macros\Route;

use App\Macros\Contracts\Macro;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

final class LivewireLocalizedMacro extends Macro {
    protected static function macroable(): string {
        return Route::class;
    }

    protected static function name(): string {
        return 'livewireLocalized';
    }

    public static function handle(string $trans_name, string $component): RoutingRoute {
        return Route::livewire(LaravelLocalization::transRoute("routes.{$trans_name}"), $component);
    }
}
