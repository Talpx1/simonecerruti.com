<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

/**
 * Tracking state for a request that opens a brand-new session, with the visit
 * source already resolved. Carried from TrackVisit::handle() to ::terminate().
 */
readonly class NewSessionState {
    public function __construct(
        public bool $consent,
        public string $new_session_id,
        public VisitSourceData $source,
        public ?string $visitor_id = null,
        public ?string $user_agent = null,
    ) {}
}
