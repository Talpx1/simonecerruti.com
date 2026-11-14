<?php

declare(strict_types=1);

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

use function Safe\preg_match_all;

final class CalculateReadingTime {
    use AsAction;

    public function handle(string $html, int $words_per_minute = 220): int {
        $text = \html_entity_decode(\strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $word_count = preg_match_all('/[\p{L}\p{N}]+(?:[\'’\-][\p{L}\p{N}]+)*/u', $text);

        return \max(1, (int) \ceil($word_count / \max(1, $words_per_minute)));
    }
}
