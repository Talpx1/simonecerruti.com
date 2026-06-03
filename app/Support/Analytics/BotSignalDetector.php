<?php

declare(strict_types=1);

namespace App\Support\Analytics;

use function Safe\preg_match;

class BotSignalDetector {
    /**
     * User-Agent families whose engines always emit Sec-Fetch-* metadata
     * headers on navigation requests. A request whose UA claims to be one of
     * these but carries no Sec-Fetch-Site header is almost certainly forged.
     */
    private const SEC_FETCH_CAPABLE_PATTERN = '/(chrome|chromium|crios|edg|opr|firefox|fxios)/i';

    /**
     * Score a request's headers for signs of an automated client masquerading
     * as a browser. Matching the User-Agent string alone cannot catch a spoofed
     * agent, so this cross-checks the headers a genuine browser cannot omit:
     * Accept, Accept-Language and Accept-Encoding are always sent, and modern
     * engines additionally send Sec-Fetch-* metadata. Each missing signal adds
     * its configured weight; the caller compares the total against the
     * configured threshold via {@see self::isAutomated()}.
     */
    public static function score(
        ?string $accept,
        ?string $accept_language,
        ?string $accept_encoding,
        ?string $sec_fetch_site,
        ?string $user_agent,
    ): int {
        $score = 0;

        // Headers a genuine browser always sends; each missing one adds its weight.
        $required_headers = [
            'missing_accept' => $accept,
            'missing_accept_language' => $accept_language,
            'missing_accept_encoding' => $accept_encoding,
        ];

        foreach ($required_headers as $weight_key => $value) {
            if (blank($value)) {
                $score += config()->integer("analytics.bot_detection.weights.{$weight_key}", 0);
            }
        }

        // Sec-Fetch-* metadata is only expected from engines that emit it, so its
        // absence is scored only when the UA claims to be one of them.
        if (blank($sec_fetch_site) && self::claimsSecFetchCapableBrowser($user_agent)) {
            $score += config()->integer('analytics.bot_detection.weights.missing_sec_fetch', 0);
        }

        return $score;
    }

    public static function isAutomated(int $score): bool {
        return $score >= config()->integer('analytics.bot_detection.threshold');
    }

    private static function claimsSecFetchCapableBrowser(?string $user_agent): bool {
        return $user_agent !== null && preg_match(self::SEC_FETCH_CAPABLE_PATTERN, $user_agent) === 1;
    }
}
