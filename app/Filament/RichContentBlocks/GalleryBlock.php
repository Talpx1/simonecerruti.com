<?php

declare(strict_types=1);

namespace App\Filament\RichContentBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Storage;

final class GalleryBlock extends RichContentCustomBlock {
    public static function getId(): string {
        return 'gallery';
    }

    public static function getLabel(): string {
        return __('Gallery');
    }

    public static function configureEditorAction(Action $action): Action {
        return $action
            ->modalHeading(__('Gallery'))
            ->schema([
                Repeater::make('images')
                    ->label(__('Images'))
                    ->minItems(2)
                    ->maxItems(12)
                    ->required()
                    ->schema([
                        FileUpload::make('image')
                            ->label(__('Image'))
                            ->image()
                            ->disk('public')
                            ->directory('blog/gallery')
                            ->visibility('public')
                            ->required(),

                        TextInput::make('caption')
                            ->label(__('Caption'))
                            ->maxLength(255),
                    ]),
            ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function toPreviewHtml(array $config): string {
        return view('rich-content.blocks.gallery-preview', ['images' => self::resolveImages($config)])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string {
        return view('rich-content.blocks.gallery', ['images' => self::resolveImages($config)])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     * @return list<array{url: string, caption: string|null}>
     */
    private static function resolveImages(array $config): array {
        $images = [];

        foreach (\is_array($config['images'] ?? null) ? $config['images'] : [] as $item) {
            $path = \is_array($item) ? ($item['image'] ?? null) : null;

            if (\is_array($path)) {
                $path = \reset($path) ?: null;
            }

            if (! \is_string($path) || $path === '') {
                continue;
            }

            $caption = $item['caption'] ?? null;

            $images[] = [
                'url' => Storage::disk('public')->url($path),
                'caption' => \is_string($caption) && $caption !== '' ? $caption : null,
            ];
        }

        return $images;
    }
}
