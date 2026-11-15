<?php

declare(strict_types=1);
use App\Providers\AppMacroServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\RequestMacroServiceProvider;
use App\Providers\RouteMacroServiceProvider;
use App\Providers\StrMacroServiceProvider;
use App\Providers\TextInputMacroServiceProvider;

return [
    AppServiceProvider::class,
    AppMacroServiceProvider::class,
    TextInputMacroServiceProvider::class,
    RequestMacroServiceProvider::class,
    RouteMacroServiceProvider::class,
    StrMacroServiceProvider::class,
    AdminPanelProvider::class,
];
