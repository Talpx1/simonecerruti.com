<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Actions\Analytics\DetectVisitSource;
use App\DataTransferObjects\Analytics\ExistingSessionState;
use App\DataTransferObjects\Analytics\NewSessionState;
use App\DataTransferObjects\Analytics\VisitSourceData;
use App\Models\PageView;
use App\Models\VisitSession;
use App\Support\Analytics\DeviceTypeDetector;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit {
    /**
     * State carried from handle() to terminate(). This relies on the middleware
     * being registered as a singleton (see AppServiceProvider) so both phases
     * share the same instance. It is reset at the start of every handle() so a
     * stale value can never bleed into the next request on long-running runtimes.
     */
    private ExistingSessionState|NewSessionState|null $state = null;

    public function handle(Request $request, Closure $next): Response {
        $this->state = null;

        /** @var Response $response */
        $response = $next($request);

        if ($this->shouldTrack($request, $response)) {
            $this->state = $this->captureState($request);
        }

        return $response;
    }

    public function terminate(Request $request, Response $response): void {
        if ($this->state === null) {
            return;
        }

        $session = $this->resolveSession($request, $this->state);

        $this->recordPageView($request, $session);
    }

    /**
     * Build the tracking state and queue any cookies onto the response. Cookies
     * must be queued here in handle() (before the response is sent), not in
     * terminate() where it would be too late to reach the browser.
     */
    private function captureState(Request $request): ExistingSessionState|NewSessionState {
        $consent = $this->hasAnalyticsConsent();

        $existing_session_id = $request->cookieStringOrDefault(config()->string('analytics.session_cookie_name'));

        if ($existing_session_id !== null) {
            return new ExistingSessionState($consent, $existing_session_id);
        }

        return $this->openNewSession($request, $consent);
    }

    private function openNewSession(Request $request, bool $consent): NewSessionState {
        $session_cookie_name = config()->string('analytics.session_cookie_name');
        $visitor_cookie_name = config()->string('analytics.visitor_cookie_name');

        $new_session_id = (string) Str::uuid();
        $visitor_id = $consent
            ? $request->cookieStringOrDefault($visitor_cookie_name, (string) Str::uuid())
            : null;

        $this->queueCookie($session_cookie_name, $new_session_id, config()->integer('analytics.session_window_minutes'));

        if ($consent && $request->cookieStringOrDefault($visitor_cookie_name) === null) {
            $this->queueCookie($visitor_cookie_name, (string) $visitor_id, config()->integer('analytics.visitor_cookie_days') * 24 * 60);
        }

        return new NewSessionState(
            consent: $consent,
            new_session_id: $new_session_id,
            source: $this->resolveSource($request),
            visitor_id: $visitor_id,
            user_agent: $consent ? $request->userAgent() : null,
        );
    }

    private function resolveSession(Request $request, ExistingSessionState|NewSessionState $state): VisitSession {
        if ($state instanceof NewSessionState) {
            return $this->createSession($request, $state);
        }

        $session = VisitSession::query()
            ->whereKey($state->existing_session_id)
            ->activeWindow()
            ->first();

        if ($session !== null) {
            return $this->touchSession($session);
        }

        // The cookie points to a session that no longer matches the active
        // window (deleted row, schema reset, clock skew); recover by opening a
        // fresh session, re-resolving the data handle() skipped for this branch.
        return $this->createSession($request, $this->recoverAsNewSession($request, $state));
    }

    private function shouldTrack(Request $request, Response $response): bool {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($response->isRedirection()) {
            return false;
        }

        /** @var list<string> $skip_paths */
        $skip_paths = config()->array('analytics.skip_paths');

        return ! $request->is(...$skip_paths);
    }

    private function hasAnalyticsConsent(): bool {
        $cookie_consent = view()->shared('cookieConsent');

        return is_array($cookie_consent) && ($cookie_consent['analytics'] ?? false);
    }

    private function recoverAsNewSession(Request $request, ExistingSessionState $state): NewSessionState {
        return new NewSessionState(
            consent: $state->consent,
            new_session_id: (string) Str::uuid(),
            source: $this->resolveSource($request),
            user_agent: $state->consent ? $request->userAgent() : null,
        );
    }

    private function resolveSource(Request $request): VisitSourceData {
        return DetectVisitSource::make()->handle(
            utm_source: $request->queryStringOrNull('utm_source'),
            utm_medium: $request->queryStringOrNull('utm_medium'),
            utm_campaign: $request->queryStringOrNull('utm_campaign'),
            utm_term: $request->queryStringOrNull('utm_term'),
            utm_content: $request->queryStringOrNull('utm_content'),
            referrer_url: $request->headers->get('referer'),
            current_host: $request->getHost(),
        );
    }

    private function queueCookie(string $name, string $value, int $minutes): void {
        Cookie::queue(
            $name,
            $value,
            $minutes,
            '/',
            null,
            (bool) config('session.secure'),
            true,
            false,
            'Lax',
        );
    }

    private function touchSession(VisitSession $session): VisitSession {
        $session->forceFill([
            'last_activity_at' => now(),
            'pageview_count' => $session->pageview_count + 1,
        ])->save();

        return $session;
    }

    private function createSession(Request $request, NewSessionState $state): VisitSession {
        $source = $state->source;

        // Coarse device classification (incl. bot detection) is derived
        // transiently from the request User-Agent regardless of consent: it is
        // not stored as an identifier and is needed to filter non-human traffic.
        // The raw user agent, IP and visitor_id stay gated behind consent.
        $detected_user_agent = $request->userAgent();

        return VisitSession::query()->create([
            'id' => $state->new_session_id,
            'visitor_id' => $state->visitor_id,
            'source' => $source->source,
            'medium' => $source->medium,
            'campaign_id' => $source->campaign_id,
            'utm_source' => $source->utm_source,
            'utm_medium' => $source->utm_medium,
            'utm_campaign' => $source->utm_campaign,
            'utm_term' => $source->utm_term,
            'utm_content' => $source->utm_content,
            'referrer_url' => $source->referrer_url,
            'referrer_host' => $source->referrer_host,
            'landing_path' => $this->currentPath($request),
            'landing_route_name' => $request->route()?->getName(),
            'locale' => app()->getLocale(),
            'ip' => $state->consent ? $request->ip() : null,
            'user_agent' => $state->user_agent,
            'device_type' => is_string($detected_user_agent) && $detected_user_agent !== ''
                ? DeviceTypeDetector::detect($detected_user_agent)->value
                : null,
            'country' => $request->headers->get('CF-IPCountry') ?: null,
            'consent_analytics' => $state->consent,
            'started_at' => now(),
            'last_activity_at' => now(),
            'pageview_count' => 1,
        ]);
    }

    private function recordPageView(Request $request, VisitSession $session): void {
        PageView::query()->create([
            'visit_session_id' => $session->id,
            'url_path' => $this->currentPath($request),
            'route_name' => $request->route()?->getName(),
            'locale' => app()->getLocale(),
            'viewed_at' => now(),
        ]);
    }

    private function currentPath(Request $request): string {
        return '/'.ltrim($request->path(), '/');
    }
}
