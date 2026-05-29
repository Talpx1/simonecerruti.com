<?php

declare(strict_types=1);

namespace App\Support\Analytics;

use App\Enums\DeviceType;

class DeviceTypeDetector {
    private const BOT_PATTERN = '/(bot|crawler|spider|crawling|slurp|facebookexternalhit|embedly|preview|fetch|googleother|google-inspectiontool|chatgpt|claudebot|ccbot|gptbot|whatsapp|telegrambot|petalbot|bytespider|semrush|ahrefs|mj12bot)/i';

    private const TABLET_PATTERN = '/(ipad|tablet|playbook|silk|kindle|nexus 7|nexus 9|nexus 10|sm-t|gt-p|sm-p)/i';

    private const MOBILE_PATTERN = '/(mobi|iphone|ipod|android.*mobile|windows phone|blackberry|opera mini|opera mobi|iemobile)/i';

    private const DESKTOP_PATTERN = '/(windows nt|macintosh|x11|linux|cros)/i';

    public static function detect(string $user_agent): DeviceType {
        if ($user_agent === '') {
            return DeviceType::UNKNOWN;
        }

        return match (true) {
            preg_match(self::BOT_PATTERN, $user_agent) === 1 => DeviceType::BOT,
            preg_match(self::TABLET_PATTERN, $user_agent) === 1 => DeviceType::TABLET,
            preg_match(self::MOBILE_PATTERN, $user_agent) === 1 => DeviceType::MOBILE,
            preg_match(self::DESKTOP_PATTERN, $user_agent) === 1 => DeviceType::DESKTOP,
            default => DeviceType::UNKNOWN,
        };
    }
}
