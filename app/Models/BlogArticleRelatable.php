<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BlogArticleRelatable extends Model {
    /** @return BelongsTo<BlogArticle, $this> */
    public function article(): BelongsTo {
        return $this->belongsTo(BlogArticle::class, 'blog_article_id');
    }

    /** @return MorphTo<Model, $this> */
    public function relatable(): MorphTo {
        return $this->morphTo();
    }

    /** @return Attribute<string|null, never> */
    public function showRoute(): Attribute {
        return Attribute::get(fn () => match ($this->relatable_type) {
            Project::class => route('project.show', $this->relatable->slug),
            BlogArticle::class => route('blog_article.show', $this->relatable->slug),
            default => null
        });
    }

    /** @return Attribute<string|null, never> */
    public function title(): Attribute {
        return Attribute::get(fn () => match ($this->relatable_type) {
            Project::class => $this->relatable->title,
            BlogArticle::class => $this->relatable->title,
            default => null
        });
    }

    /** @return Attribute<string|null, never> */
    public function postType(): Attribute {
        return Attribute::get(fn () => match ($this->relatable_type) {
            Project::class => __('Project'),
            BlogArticle::class => __('Blog article'),
            default => null
        });
    }
}
