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

    protected function mutateFormDataBeforeSave(array $data): array {
        $status = $data['status'] ?? null;

        if (is_string($status)) {
            $status = BlogArticleStatuses::tryFrom($status);
        }

        if (! ($data['published_at'] ?? null) && $status === BlogArticleStatuses::PUBLISHED) {
            $data['published_at'] = $this->data['published_at'] = now();
        }

        return $data;
    }
}
