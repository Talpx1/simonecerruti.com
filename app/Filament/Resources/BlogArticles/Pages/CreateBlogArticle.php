<?php

declare(strict_types=1);

namespace App\Filament\Resources\BlogArticles\Pages;

use App\Enums\BlogArticleStatuses;
use App\Filament\Resources\BlogArticles\BlogArticleResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateBlogArticle extends CreateRecord {
    use Translatable;

    protected static string $resource = BlogArticleResource::class;

    protected function getHeaderActions(): array {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array {
        /** @var User */
        $user = auth()->user();
        $data['author_id'] = $user->id;

        $status = $data['status'] ?? null;

        if (is_string($status)) {
            $status = BlogArticleStatuses::tryFrom($status);
        }

        if (! ($data['published_at'] ?? null) && $status === BlogArticleStatuses::PUBLISHED) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
