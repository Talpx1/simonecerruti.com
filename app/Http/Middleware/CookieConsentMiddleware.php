<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reads the consent cookie and exposes the parsed preferences to all views via a shared
 * "cookieConsent" variable, so Blade layouts can conditionally load third-party scripts
 * server-side (for example skipping the Clarity script when analytics consent was not given).
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

        if (is_string($raw)) {
            try {
                $prefs = \Safe\json_decode($raw, true);
            } catch (\JsonException) {
                $prefs = null;
            }

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

        /** @var Response $response */
        $response = $next($request);

        return $response;
    }
}
