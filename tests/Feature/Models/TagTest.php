<?php

declare(strict_types=1);

use App\Enums\ServiceAreas;
use App\Enums\TagTypes;
use App\Models\Tag;

/**
 * @param  array<string, mixed>  $attributes
 */
function createTag(array $attributes = []): Tag {
    return Tag::query()->create([
        'name' => ['it' => 'Laravel', 'en' => 'Laravel'],
        'type' => TagTypes::TAG->value,
        ...$attributes,
    ]);
}

describe('attributes', function () {
    describe('description', function () {
        it('is translatable per locale', function () {
            $tag = createTag(['description' => ['it' => 'Ciao', 'en' => 'Hello']]);

            expect($tag->getTranslation('description', 'it'))->toBe('Ciao')
                ->and(withLocale('en', fn () => $tag->description))->toBe('Hello');
        });

        it('can be null', function () {
            expect(createTag()->refresh()->description)->toBeNull();
        });
    });

    describe('service', function () {
        it('is cast to the ServiceAreas enum', function () {
            $tag = createTag(['service' => ServiceAreas::WEB_ECOMMERCE_PLATFORMS]);

            expect($tag->refresh()->service)->toBe(ServiceAreas::WEB_ECOMMERCE_PLATFORMS)
                ->and($tag->service->getRouteName())->toBe('services.web_ecommerce_platforms');
        });

        it('accepts the raw backed value', function () {
            $tag = createTag(['service' => 'management_erp_crm']);

            expect($tag->refresh()->service)->toBe(ServiceAreas::MANAGEMENT_ERP_CRM);
        });

        it('is null when not set', function () {
            expect(createTag()->refresh()->service)->toBeNull();
        });
    });
});

describe('configuration', function () {
    it('is the model used by the tags package', function () {
        expect(config('tags.tag_model'))->toBe(Tag::class);
    });
});
