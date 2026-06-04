<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

/**
 * Tracking state for a request that arrived with a session cookie already
 * pointing at a session. Carried from TrackVisit::handle() to ::terminate().
 */
readonly class ExistingSessionState {
    public function __construct(
        public bool $consent,
        public string $existing_session_id,
    ) {}
}
