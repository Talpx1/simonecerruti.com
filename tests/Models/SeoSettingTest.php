<?php

declare(strict_types=1);

use App\Enums\SchemaType;
use App\Models\SeoSetting;

describe('attributes', function (): void {
    describe('description', function (): void {
        it('is stored translatably', function (): void {
            $settings = SeoSetting::factory()->create([
                'description' => ['en' => 'Bio', 'it' => 'Biografia'],
            ]);

            expect($settings->refresh()->getTranslation('description', 'it'))->toBe('Biografia')
                ->and($settings->getTranslation('description', 'en'))->toBe('Bio');
        });
    });

    describe('social_profiles', function (): void {
        it('is cast to an array', function (): void {
            $settings = SeoSetting::factory()->create([
                'social_profiles' => ['https://example.test/a', 'https://example.test/b'],
            ]);

            expect($settings->refresh()->social_profiles)->toBe(['https://example.test/a', 'https://example.test/b']);
        });
    });

    describe('website_schema', function (): void {
        it('is cast to a boolean', function (): void {
            $settings = SeoSetting::factory()->create(['website_schema' => false]);

            expect($settings->refresh()->website_schema)->toBeFalse();
        });
    });
});

describe('accessors', function (): void {
    describe('current', function (): void {
        it('provides a default unsaved singleton when none exists', function (): void {
            $settings = SeoSetting::current();

            expect($settings->exists)->toBeFalse()
                ->and($settings->type)->toBe(SchemaType::PERSON)
                ->and($settings->title_separator)->toBe(' | ')
                ->and($settings->website_schema)->toBeTrue();
        });

        it('returns the persisted singleton once saved', function (): void {
            expect(SeoSetting::current()->exists)->toBeFalse()
                ->and(SeoSetting::factory()->create(['title_separator' => ' — ']))->not->toBeNull()
                ->and(SeoSetting::current()->exists)->toBeTrue()
                ->and(SeoSetting::current()->title_separator)->toBe(' — ');
        });

        it('invalidates the cached singleton when settings are saved', function (): void {
            expect(SeoSetting::current()->title_separator)->toBe(' | ')
                ->and(SeoSetting::factory()->create(['title_separator' => ' · ']))->not->toBeNull()
                ->and(SeoSetting::current()->title_separator)->toBe(' · ');
        });

        it('is restored as a model instance from a serializing cache store', function (): void {
            config()->set('cache.stores.serialized', ['driver' => 'array', 'serialize' => true]);
            config()->set('cache.default', 'serialized');

            SeoSetting::factory()->create(['title_separator' => ' · ']);
            SeoSetting::current();

            expect(SeoSetting::current())->toBeInstanceOf(SeoSetting::class)
                ->and(SeoSetting::current()->title_separator)->toBe(' · ');
        });
    });
});
