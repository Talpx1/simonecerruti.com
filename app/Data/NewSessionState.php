<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

/**
 * Tracking state for a request that opens a brand-new session, with the visit
 * source already resolved. Carried from TrackVisit::handle() to ::terminate().
 */
class NewSessionState extends Data {
    public function __construct(
        public readonly bool $consent,
        public readonly string $new_session_id,
        public readonly VisitSourceData $source,
        public readonly ?string $visitor_id = null,
        public readonly ?string $user_agent = null,
    ) {}
}
