<?php

declare(strict_types=1);

use App\Enums\SchemaType;
use App\Models\BlogArticle;
use App\Models\Seo;
use Illuminate\Database\QueryException;

describe('attributes', function (): void {
    describe('title', function (): void {
        it('is stored and retrieved per locale', function (): void {
            $seo = Seo::factory()->create([
                'title' => ['en' => 'English title', 'it' => 'Titolo italiano'],
            ]);

            $seo->refresh();

            expect($seo->getTranslation('title', 'en'))->toBe('English title')
                ->and($seo->getTranslation('title', 'it'))->toBe('Titolo italiano')
                ->and(runWithLocale('it', fn () => Seo::query()->findOrFail($seo->id)->title))->toBe('Titolo italiano');
        });
    });

    describe('robots', function (): void {
        it('is cast to an array per locale', function (): void {
            $seo = Seo::factory()->create([
                'robots' => ['en' => ['noindex', 'nofollow'], 'it' => []],
            ]);

            $seo->refresh();

            expect($seo->getTranslation('robots', 'en'))->toBe(['noindex', 'nofollow'])
                ->and($seo->getTranslation('robots', 'it'))->toBe([]);
        });
    });

    describe('schema_overrides', function (): void {
        it('is cast to an array per locale', function (): void {
            $seo = Seo::factory()->create([
                'schema_overrides' => ['en' => ['headline' => 'Custom headline'], 'it' => []],
            ]);

            $seo->refresh();

            expect($seo->getTranslation('schema_overrides', 'en'))->toBe(['headline' => 'Custom headline'])
                ->and($seo->getTranslation('schema_overrides', 'it'))->toBe([]);
        });
    });

    describe('schema_type', function (): void {
        it('is cast to the SchemaType enum', function (): void {
            $seo = Seo::factory()->create(['schema_type' => SchemaType::CREATIVE_WORK]);

            expect($seo->fresh()->schema_type)->toBe(SchemaType::CREATIVE_WORK);
        });
    });
});

describe('relationships', function (): void {
    describe('seoable', function (): void {
        it('belongs to its seoable model', function (): void {
            $seo = Seo::factory()->create();

            expect($seo->seoable)->toBeInstanceOf(BlogArticle::class);
        });

        it('enforces a single seo row per model', function (): void {
            $article = BlogArticle::factory()->create();
            $attributes = ['seoable_id' => $article->id, 'seoable_type' => $article->getMorphClass()];

            Seo::factory()->create($attributes);

            expect(fn () => Seo::factory()->create($attributes))
                ->toThrow(QueryException::class, 'duplicate key value violates unique constraint');
        });
    });
});
