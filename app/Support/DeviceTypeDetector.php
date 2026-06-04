<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\DeviceType;
use DeviceDetector\DeviceDetector;

use function Safe\preg_match;

class DeviceTypeDetector {
    /**
     * Matomo device names collapsed into our coarse MOBILE bucket. Anything
     * handheld is treated as mobile; tablets keep their own bucket and large
     * screens fall into DESKTOP (see DESKTOP_DEVICES).
     *
     * @var list<string>
     */
    private const MOBILE_DEVICES = ['smartphone', 'feature phone', 'phablet', 'portable media player', 'wearable'];

    /** @var list<string> */
    private const DESKTOP_DEVICES = ['desktop', 'tv', 'console', 'smart display', 'car browser', 'smart speaker'];

    /**
     * Last-resort substring patterns for clients Matomo leaves unclassified:
     * link-preview crawlers it treats as a "mobile app" (e.g. WhatsApp) and
     * proxy browsers with no resolvable device (e.g. Opera Mini). They mirror
     * the pre-Matomo detector and are consulted ONLY when Matomo yields
     * UNKNOWN, so they never override a positive Matomo verdict.
     */
    private const FALLBACK_BOT_PATTERN = '/(bot|crawler|spider|crawling|slurp|facebookexternalhit|embedly|preview|fetch|whatsapp|telegram|petalbot|bytespider|semrush|ahrefs|mj12bot)/i';

    private const FALLBACK_MOBILE_PATTERN = '/(mobi|iphone|ipod|android.*mobile|windows phone|blackberry|opera mini|opera mobi|iemobile)/i';

    /**
     * Resolve the coarse device bucket for a User-Agent using Matomo's
     * device-detector database. Self-declared bots are detected here; forged
     * agents that claim to be a browser are handled separately by
     * BotSignalDetector via the request headers.
     */
    public static function detect(string $user_agent): DeviceType {
        if ($user_agent === '') {
            return DeviceType::UNKNOWN;
        }

        $detector = new DeviceDetector($user_agent);
        $detector->parse();

        // Self-declared crawlers, plus HTTP libraries/tools (curl, python-requests,
        // Go-http-client, …) that Matomo classifies as a "library" client rather
        // than a bot — both are automated, non-human traffic.
        if ($detector->isBot() || $detector->getClient('type') === 'library') {
            return DeviceType::BOT;
        }

        $device_name = $detector->getDeviceName();

        // Matomo's remaining device names ('camera', 'peripheral') and any UA it
        // cannot resolve a device for intentionally fall through to UNKNOWN: they
        // are not meaningful in a human/mobile/desktop breakdown.
        $device_type = match (true) {
            $device_name === 'tablet' => DeviceType::TABLET,
            in_array($device_name, self::MOBILE_DEVICES, true) => DeviceType::MOBILE,
            in_array($device_name, self::DESKTOP_DEVICES, true) => DeviceType::DESKTOP,
            default => DeviceType::UNKNOWN,
        };

        return $device_type === DeviceType::UNKNOWN
            ? self::detectFromFallbackPatterns($user_agent)
            : $device_type;
    }

    /**
     * Recover clients Matomo cannot bucket (it returned UNKNOWN) via substring
     * matching, preserving the coverage of the pre-Matomo detector for cases
     * such as WhatsApp link previews and Opera Mini.
     */
    private static function detectFromFallbackPatterns(string $user_agent): DeviceType {
        return match (true) {
            preg_match(self::FALLBACK_BOT_PATTERN, $user_agent) === 1 => DeviceType::BOT,
            preg_match(self::FALLBACK_MOBILE_PATTERN, $user_agent) === 1 => DeviceType::MOBILE,
            default => DeviceType::UNKNOWN,
        };
    }
}
