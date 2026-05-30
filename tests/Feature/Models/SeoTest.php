<?php

declare(strict_types=1);

use App\Enums\SchemaType;
use App\Models\BlogArticle;
use App\Models\Seo;
use Illuminate\Database\QueryException;

it('stores and retrieves translatable fields per locale', function () {
    $seo = Seo::factory()->create([
        'title' => ['en' => 'English title', 'it' => 'Titolo italiano'],
    ]);

    $seo->refresh();

    expect($seo->getTranslation('title', 'en'))->toBe('English title')
        ->and($seo->getTranslation('title', 'it'))->toBe('Titolo italiano')
        ->and(withLocale('it', fn () => Seo::findOrFail($seo->id)->title))->toBe('Titolo italiano');
});

it('casts robots and schema_overrides to arrays per locale', function () {
    $seo = Seo::factory()->create([
        'robots' => ['en' => ['noindex', 'nofollow'], 'it' => []],
        'schema_overrides' => ['en' => ['headline' => 'Custom headline'], 'it' => []],
    ]);

    $seo->refresh();

    expect($seo->getTranslation('robots', 'en'))->toBe(['noindex', 'nofollow'])
        ->and($seo->getTranslation('robots', 'it'))->toBe([])
        ->and($seo->getTranslation('schema_overrides', 'en'))->toBe(['headline' => 'Custom headline']);
});

it('casts schema_type to the SchemaType enum', function () {
    $seo = Seo::factory()->create(['schema_type' => SchemaType::CREATIVE_WORK]);

    expect($seo->fresh()->schema_type)->toBe(SchemaType::CREATIVE_WORK);
});

it('belongs to its seoable model', function () {
    $seo = Seo::factory()->create();

    expect($seo->seoable)->toBeInstanceOf(BlogArticle::class);
});

it('enforces a single seo row per model', function () {
    $article = BlogArticle::factory()->create();
    $attributes = ['seoable_id' => $article->id, 'seoable_type' => $article->getMorphClass()];

    Seo::factory()->create($attributes);

    expect(fn () => Seo::factory()->create($attributes))->toThrow(QueryException::class);
});
