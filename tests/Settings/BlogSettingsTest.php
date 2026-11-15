<?php

declare(strict_types=1);

use App\Settings\BlogSettings;

describe('author_bio', function (): void {
    it('defaults to an empty array', function (): void {
        expect(resolve(BlogSettings::class)->author_bio)->toBeEmpty();
    });
});

describe('getAuthorBioForCurrentLocale', function (): void {
    it('returns the bio for the current locale', function (): void {
        $settings = resolve(BlogSettings::class);
        $settings->author_bio = ['it' => 'Ciao', 'en' => 'Hello'];

        expect(runWithLocale('it', fn (): ?string => $settings->getAuthorBioForCurrentLocale()))->toBe('Ciao')
            ->and(runWithLocale('en', fn (): ?string => $settings->getAuthorBioForCurrentLocale()))->toBe('Hello');
    });

    it('returns null when the bio of the current locale is blank', function (): void {
        $settings = resolve(BlogSettings::class);
        $settings->author_bio = ['it' => '  ', 'en' => ''];

        expect(runWithLocale('it', fn (): ?string => $settings->getAuthorBioForCurrentLocale()))->toBeNull()
            ->and(runWithLocale('en', fn (): ?string => $settings->getAuthorBioForCurrentLocale()))->toBeNull();
    });

    it('returns null when the current locale has no bio', function (): void {
        $settings = resolve(BlogSettings::class);
        $settings->author_bio = ['it' => 'Ciao'];

        expect(runWithLocale('de', fn (): ?string => $settings->getAuthorBioForCurrentLocale()))->toBeNull();
    });

    it('persists the values through save', function (): void {
        $settings = resolve(BlogSettings::class);

        expect(runWithLocale('en', fn (): ?string => $settings->getAuthorBioForCurrentLocale()))->toBeNull();

        $settings->author_bio = ['en' => 'Saved'];
        $settings->save();
        app()->forgetInstance(BlogSettings::class);

        expect(runWithLocale('en', fn (): ?string => resolve(BlogSettings::class)->getAuthorBioForCurrentLocale()))->toBe('Saved');
    });
});
