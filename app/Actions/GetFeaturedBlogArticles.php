<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\BlogArticle;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetFeaturedBlogArticles {
    use AsAction;

    /**
     * @return Collection<int, BlogArticle>
     */
    public function handle(int $limit = 2): Collection {
        return BlogArticle::query()
            ->wherePublished()
            ->whereHasCurrentLocaleTranslation()
            ->where('featured', true)
            ->with(['tags', 'media', 'author'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }
}
