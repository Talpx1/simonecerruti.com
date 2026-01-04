<?php

declare(strict_types=1);

namespace App\Filament\Resources\BlogArticles\Pages;

use App\Enums\BlogArticleStatuses;
use App\Filament\Resources\BlogArticles\BlogArticleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditBlogArticle extends EditRecord {
    use Translatable;

    protected static string $resource = BlogArticleResource::class;

    protected function getHeaderActions(): array {
        return [
            DeleteAction::make(),
            LocaleSwitcher::make(),
        ];
    }

    protected function beforeSave(): void {
        if (! $this->data['published_at'] && $this->data['status'] === BlogArticleStatuses::PUBLISHED->value) {
            $this->data['published_at'] = now();
        }
    }
}
