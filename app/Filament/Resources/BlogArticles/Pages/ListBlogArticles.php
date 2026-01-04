<?php

declare(strict_types=1);

namespace App\Filament\Resources\BlogArticles\Pages;

use App\Filament\Resources\BlogArticles\BlogArticleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBlogArticles extends ListRecords {
    protected static string $resource = BlogArticleResource::class;

    protected function getHeaderActions(): array {
        return [
            CreateAction::make(),
        ];
    }

    public function getModelLabel(): string {
        return __('resources.blog_article.label');
    }

    public function getPluralModelLabel(): string {
        return __('resources.blog_article.plural_label');
    }
}
