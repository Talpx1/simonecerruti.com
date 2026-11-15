<?php

declare(strict_types=1);

namespace App\Actions;

use Dom\Element;
use Dom\HTMLDocument;
use Dom\HTMLElement;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class AddHeadingIds {
    use AsAction;

    public function handle(string $html): string {
        $document = HTMLDocument::createFromString(
            '<body>'.$html.'</body>',
            LIBXML_NOERROR,
            'UTF-8',
        );

        $headings = $document->querySelectorAll('h2, h3');
        $used_ids = $this->collectExistingHeadingIds($headings);

        $this->assignIdsToHeadingsWithoutOne($headings, $used_ids);

        $body = $document->body;

        return $body instanceof HTMLElement ? $body->innerHTML : '';
    }

    /**
     * @param  iterable<Element>  $headings
     * @return array<string, true>
     */
    private function collectExistingHeadingIds(iterable $headings): array {
        $used_ids = [];

        foreach ($headings as $heading) {
            $existing_id = $heading->getAttribute('id');

            if ($existing_id !== null && $existing_id !== '') {
                $used_ids[$existing_id] = true;
            }
        }

        return $used_ids;
    }

    /**
     * @param  iterable<Element>  $headings
     * @param  array<string, true>  $used_ids
     */
    private function assignIdsToHeadingsWithoutOne(iterable $headings, array &$used_ids): void {
        foreach ($headings as $heading) {
            $existing_id = $heading->getAttribute('id');

            if ($existing_id === null || $existing_id === '') {
                $heading->setAttribute('id', $this->makeUniqueId(\trim((string) $heading->textContent), $used_ids));
            }
        }
    }

    /**
     * @param  array<string, true>  $used_ids
     */
    private function makeUniqueId(string $title, array &$used_ids): string {
        $base = Str::slug($title) ?: 'section';
        $candidate = $base;
        $suffix = 2;

        while (isset($used_ids[$candidate])) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        $used_ids[$candidate] = true;

        return $candidate;
    }
}
