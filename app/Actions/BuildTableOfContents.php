<?php

declare(strict_types=1);

namespace App\Actions;

use Dom\HTMLDocument;
use Dom\HTMLElement;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class BuildTableOfContents {
    use AsAction;

    /**
     * @return array{html: string, items: list<array{id: string, title: string, level: int}>}
     */
    public function handle(string $html): array {
        $document = HTMLDocument::createFromString(
            '<body>'.$html.'</body>',
            LIBXML_NOERROR,
            'UTF-8',
        );

        $headings = $document->querySelectorAll('h2, h3');
        $used_ids = [];

        foreach ($headings as $heading) {
            $existing_id = $heading->getAttribute('id');

            if ($existing_id !== null && $existing_id !== '') {
                $used_ids[$existing_id] = true;
            }
        }

        $items = [];

        foreach ($headings as $heading) {
            $title = \trim((string) $heading->textContent);
            $id = $heading->getAttribute('id');

            if ($id === null || $id === '') {
                $id = $this->makeUniqueId($title, $used_ids);
                $heading->setAttribute('id', $id);
            }

            $items[] = [
                'id' => $id,
                'title' => $title,
                'level' => (int) \substr($heading->localName, 1),
            ];
        }

        $body = $document->body;

        return [
            'html' => $body instanceof HTMLElement ? $body->innerHTML : '',
            'items' => $items,
        ];
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
