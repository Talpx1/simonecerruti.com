<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Actions\Analytics\DetectVisitSource;
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
     * Request attribute key used to carry state from handle() to terminate().
     * We can't use instance properties: Laravel resolves middleware via the
     * container in both phases, getting a fresh instance each time.
     */
    private const STATE_KEY = 'track_visit.state';

    public function handle(Request $request, Closure $next): Response {
        /** @var Response $response */
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        $consent = (bool) (view()->shared('cookieConsent')['analytics'] ?? false);

        $session_cookie_name = config()->string('analytics.session_cookie_name');
        $visitor_cookie_name = config()->string('analytics.visitor_cookie_name');
        $window_minutes = config()->integer('analytics.session_window_minutes');
        $visitor_days = config()->integer('analytics.visitor_cookie_days');

        $existing_session_id = $request->cookieStringOrDefault($session_cookie_name);

        if ($existing_session_id !== null) {
            $request->attributes->set(self::STATE_KEY, [
                'existing_session_id' => $existing_session_id,
                'new_session_id' => null,
                'visitor_id' => null,
                'consent' => $consent,
                'source' => null,
                'user_agent' => null,
            ]);

            return $response;
        }

        $new_session_id = (string) Str::uuid();
        $visitor_id = $consent
            ? $request->cookieStringOrDefault($visitor_cookie_name, (string) Str::uuid())
            : null;

        Cookie::queue(
            $session_cookie_name,
            $new_session_id,
            $window_minutes,
            '/',
            null,
            (bool) config('session.secure'),
            true,
            false,
            'Lax',
        );

        if ($consent && $request->cookieStringOrDefault($visitor_cookie_name) === null) {
            Cookie::queue(
                $visitor_cookie_name,
                (string) $visitor_id,
                $visitor_days * 24 * 60,
                '/',
                null,
                (bool) config('session.secure'),
                true,
                false,
                'Lax',
            );
        }

        $request->attributes->set(self::STATE_KEY, [
            'existing_session_id' => null,
            'new_session_id' => $new_session_id,
            'visitor_id' => $visitor_id,
            'consent' => $consent,
            'source' => DetectVisitSource::run(
                utm_source: $request->queryStringOrNull('utm_source'),
                utm_medium: $request->queryStringOrNull('utm_medium'),
                utm_campaign: $request->queryStringOrNull('utm_campaign'),
                utm_term: $request->queryStringOrNull('utm_term'),
                utm_content: $request->queryStringOrNull('utm_content'),
                referrer_url: $request->headers->get('referer'),
                current_host: $request->getHost(),
            ),
            'user_agent' => $consent ? $request->userAgent() : null,
        ]);

        return $response;
    }

    public function terminate(Request $request, Response $response): void {
        /**
         * @var array{
         *     existing_session_id: string|null,
         *     new_session_id: string|null,
         *     visitor_id: string|null,
         *     consent: bool,
         *     source: VisitSourceData|null,
         *     user_agent: string|null
         * }|null $state
         */
        $state = $request->attributes->get(self::STATE_KEY);

        if ($state === null) {
            return;
        }

        if ($state['existing_session_id'] !== null) {
            $session = VisitSession::query()
                ->whereKey($state['existing_session_id'])
                ->activeWindow()
                ->first();

            if ($session === null) {
                $session = $this->createSession($request, $this->fallbackStateForExpiredCookie($request, $state));
            } else {
                $session->forceFill([
                    'last_activity_at' => now(),
                    'pageview_count' => $session->pageview_count + 1,
                ])->save();
            }
        } else {
            $session = $this->createSession($request, $state);
        }

        PageView::query()->create([
            'visit_session_id' => $session->id,
            'url_path' => '/'.ltrim($request->path(), '/'),
            'route_name' => $request->route()?->getName(),
            'locale' => app()->getLocale(),
            'viewed_at' => now(),
        ]);
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

    /**
     * When a `vs_id` cookie points to a session that no longer matches the
     * active window (deleted row, schema reset, clock skew), we recover by
     * treating the request as a new session. We still need to detect the
     * source, since handle() skipped detection on the existing-cookie branch.
     *
     * @param  array{existing_session_id: string|null, new_session_id: string|null, visitor_id: string|null, consent: bool, source: VisitSourceData|null, user_agent: string|null}  $state
     * @return array{existing_session_id: null, new_session_id: string, visitor_id: string|null, consent: bool, source: VisitSourceData, user_agent: string|null}
     */
    private function fallbackStateForExpiredCookie(Request $request, array $state): array {
        return [
            'existing_session_id' => null,
            'new_session_id' => (string) Str::uuid(),
            'visitor_id' => $state['visitor_id'],
            'consent' => $state['consent'],
            'source' => DetectVisitSource::run(
                utm_source: $request->queryStringOrNull('utm_source'),
                utm_medium: $request->queryStringOrNull('utm_medium'),
                utm_campaign: $request->queryStringOrNull('utm_campaign'),
                utm_term: $request->queryStringOrNull('utm_term'),
                utm_content: $request->queryStringOrNull('utm_content'),
                referrer_url: $request->headers->get('referer'),
                current_host: $request->getHost(),
            ),
            'user_agent' => $state['consent'] ? $request->userAgent() : null,
        ];
    }

    /**
     * @param  array{existing_session_id: string|null, new_session_id: string|null, visitor_id: string|null, consent: bool, source: VisitSourceData|null, user_agent: string|null}  $state
     */
    private function createSession(Request $request, array $state): VisitSession {
        $source = $state['source'];
        $user_agent = $state['user_agent'];

        return VisitSession::query()->create([
            'id' => $state['new_session_id'],
            'visitor_id' => $state['visitor_id'],
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
            'landing_path' => '/'.ltrim($request->path(), '/'),
            'landing_route_name' => $request->route()?->getName(),
            'locale' => app()->getLocale(),
            'ip' => $state['consent'] ? $request->ip() : null,
            'user_agent' => $user_agent,
            'device_type' => $state['consent'] && is_string($user_agent) && $user_agent !== ''
                ? DeviceTypeDetector::detect($user_agent)->value
                : null,
            'country' => $request->headers->get('CF-IPCountry') ?: null,
            'consent_analytics' => $state['consent'],
            'started_at' => now(),
            'last_activity_at' => now(),
            'pageview_count' => 1,
        ]);
    }
}
