<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

/**
 * Tracking state for a request that arrived with a session cookie already
 * pointing at a session. Carried from TrackVisit::handle() to ::terminate().
 */
class ExistingSessionState extends Data {
    public function __construct(
        public readonly bool $consent,
        public readonly string $existing_session_id,
    ) {}
}
