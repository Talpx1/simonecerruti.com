<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale {
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        /** @var string */
        $locale = $request->cookie('locale');

        if (! $locale || ! in_array($locale, config()->array('app.available_locales'))) {
            $locale = config()->string('app.fallback_locale', 'en');
        }

        Session::put('locale', $locale);

        App::setLocale($locale);

        return $next($request);
    }
}
