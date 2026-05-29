<?php

declare(strict_types=1);

use App\Enums\DeviceType;
use App\Support\Analytics\DeviceTypeDetector;

dataset('user_agents', [
    'iPhone Safari is mobile' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1', DeviceType::MOBILE],
    'Android Chrome is mobile' => ['Mozilla/5.0 (Linux; Android 14; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36', DeviceType::MOBILE],
    'iPad is tablet' => ['Mozilla/5.0 (iPad; CPU OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Safari/604.1', DeviceType::TABLET],
    'Samsung tablet is tablet' => ['Mozilla/5.0 (Linux; Android 13; SM-T970) AppleWebKit/537.36 Chrome/124.0.0.0 Safari/537.36', DeviceType::TABLET],
    'Windows Chrome is desktop' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36', DeviceType::DESKTOP],
    'macOS Safari is desktop' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 14_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Safari/605.1.15', DeviceType::DESKTOP],
    'Googlebot is bot' => ['Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', DeviceType::BOT],
    'Bingbot is bot' => ['Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)', DeviceType::BOT],
    'Empty string is unknown' => ['', DeviceType::UNKNOWN],
    'Garbage is unknown' => ['lorem-ipsum-foo', DeviceType::UNKNOWN],
]);

it('detects the expected device type', function (string $ua, DeviceType $expected) {
    expect(DeviceTypeDetector::detect($ua))->toBe($expected);
})->with('user_agents');
