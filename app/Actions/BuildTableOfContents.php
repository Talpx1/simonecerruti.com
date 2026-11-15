<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\TableOfContentsEntryData;
use Dom\HTMLDocument;
use Lorisleiva\Actions\Concerns\AsAction;

final class BuildTableOfContents {
    use AsAction;

    /**
     * @return list<TableOfContentsEntryData>
     */
    public function handle(string $html): array {
        $document = HTMLDocument::createFromString(
            '<body>'.$html.'</body>',
            LIBXML_NOERROR,
            'UTF-8',
        );

        $entries = [];

        foreach ($document->querySelectorAll('h2, h3') as $heading) {
            $id = $heading->getAttribute('id');

            if ($id === null || $id === '') {
                continue;
            }

            $entries[] = new TableOfContentsEntryData(
                id: $id,
                title: \trim((string) $heading->textContent),
                level: (int) \substr($heading->localName, 1),
            );
        }

        return $entries;
    }
}
