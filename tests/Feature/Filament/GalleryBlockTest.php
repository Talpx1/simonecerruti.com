<?php

declare(strict_types=1);

use App\Filament\RichContentBlocks\GalleryBlock;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

it('renders the gallery block as figures inside the gallery wrapper', function () {
    $html = RichContentRenderer::make([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'customBlock',
                'attrs' => [
                    'id' => 'gallery',
                    'config' => [
                        'images' => [
                            ['image' => 'blog/gallery/a.jpg', 'caption' => 'First <caption>'],
                            ['image' => 'blog/gallery/b.jpg', 'caption' => null],
                        ],
                    ],
                ],
            ],
        ],
    ])
        ->customBlocks([GalleryBlock::class])
        ->toHtml();

    expect($html)
        ->toContain('class="gallery"')
        ->toContain('<figure>')
        ->toContain('blog/gallery/a.jpg')
        ->toContain('loading="lazy"')
        ->toContain('<figcaption>First &lt;caption&gt;</figcaption>')
        ->and(substr_count($html, '<figure>'))->toBe(2)
        ->and(substr_count($html, '<figcaption>'))->toBe(1);
});
