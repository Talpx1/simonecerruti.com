<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CookieConsentMiddleware
 * ───────────────────────
 * Reads the consent cookie and exposes the parsed preferences
 * to all views via a shared variable `$cookieConsent`.
 *
 * This allows Blade layouts / partials to conditionally load
 * third-party scripts server-side (e.g. skip injecting the
 * Clarity script tag if analytics consent was not given).
 *
 * Usage in layouts:
 *
 *   @if ($cookieConsent['analytics'])
 *
 *       @include('partials.scripts.clarity')
 *
 *   @endif
 */
class CookieConsentMiddleware {
    public function handle(Request $request, Closure $next): Response {
        $defaults = [
            'given' => false,
            'necessary' => true,
            'analytics' => false,
            'functional' => false,
            'marketing' => false,
        ];

        $raw = $request->cookie('cookie_consent');

        if ($raw) {
            $prefs = \Safe\json_decode($raw, true);

            if (is_array($prefs) && isset($prefs['version'])) {
                $defaults = [
                    'given' => true,
                    'necessary' => true,
                    'analytics' => (bool) ($prefs['analytics'] ?? false),
                    'functional' => (bool) ($prefs['functional'] ?? false),
                    'marketing' => (bool) ($prefs['marketing'] ?? false),
                ];
            }
        }

        // Share with all Blade views
        view()->share('cookieConsent', $defaults);

        return $next($request);
    }
}
