<?php

declare(strict_types=1);

use App\Enums\ServiceAreas;
use App\Models\Tag;

describe('attributes', function (): void {
    describe('description', function (): void {
        it('is translatable per locale', function (): void {
            $tag = createTag(['description' => ['it' => 'Ciao', 'en' => 'Hello']]);

            expect($tag->getTranslation('description', 'it'))->toBe('Ciao')
                ->and(runWithLocale('en', fn () => $tag->description))->toBe('Hello');
        });

        it('can be null', function (): void {
            expect(createTag()->refresh()->description)->toBeNull();
        });
    });

    describe('service', function (): void {
        it('is cast to the ServiceAreas enum', function (): void {
            $tag = createTag(['service' => ServiceAreas::WEB_ECOMMERCE_PLATFORMS]);

            expect($tag->refresh()->service)->toBe(ServiceAreas::WEB_ECOMMERCE_PLATFORMS)
                ->and($tag->service->getRouteName())->toBe('services.web_ecommerce_platforms');
        });

        it('accepts the raw backed value', function (): void {
            $tag = createTag(['service' => ServiceAreas::MANAGEMENT_ERP_CRM->value]);

            expect($tag->refresh()->service)->toBe(ServiceAreas::MANAGEMENT_ERP_CRM);
        });

        it('is null when not set', function (): void {
            expect(createTag()->refresh()->service)->toBeNull();
        });
    });
});

describe('configuration', function (): void {
    it('is the model used by the tags package', function (): void {
        expect(config('tags.tag_model'))->toBe(Tag::class);
    });
});
