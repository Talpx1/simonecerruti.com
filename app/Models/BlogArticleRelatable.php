<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BlogArticleRelatableFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $blog_article_id
 * @property class-string<Model> $relatable_type
 * @property int $relatable_id
 * @property-read Project|BlogArticle|null $relatable
 * @property-read string|null $show_route
 * @property-read string|null $title
 * @property-read string|null $post_type
 */
class BlogArticleRelatable extends Model {
    /** @use HasFactory<BlogArticleRelatableFactory> */
    use HasFactory;

    /** @return BelongsTo<BlogArticle, $this> */
    public function article(): BelongsTo {
        return $this->belongsTo(BlogArticle::class, 'blog_article_id');
    }

    /** @return MorphTo<Model, $this> */
    public function relatable(): MorphTo {
        return $this->morphTo();
    }

    /** @return Attribute<string|null, never> */
    protected function showRoute(): Attribute {
        return Attribute::get(fn (): ?string => match ($this->relatable_type) {
            Project::class => route('project.show', $this->relatable?->slug),
            BlogArticle::class => route('blog_article.show', $this->relatable?->slug),
            default => null
        });
    }

    /** @return Attribute<string|null, never> */
    protected function title(): Attribute {
        return Attribute::get(fn (): ?string => match ($this->relatable_type) {
            Project::class, BlogArticle::class => $this->relatable?->title,
            default => null
        });
    }

    /** @return Attribute<string|null, never> */
    protected function postType(): Attribute {
        return Attribute::get(function (): ?string {
            $label = match ($this->relatable_type) {
                Project::class => __('Project'),
                BlogArticle::class => __('Blog article'),
                default => null,
            };

            return is_string($label) ? $label : null;
        });
    }
}
