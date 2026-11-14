<?php

declare(strict_types=1);

use App\Settings\BlogSettings;

describe('author_bio', function () {
    it('defaults to an empty array', function () {
        expect(resolve(BlogSettings::class)->author_bio)->toBeEmpty();
    });
});

describe('getAuthorBioForCurrentLocale', function () {
    it('returns the bio for the current locale', function () {
        $settings = resolve(BlogSettings::class);
        $settings->author_bio = ['it' => 'Ciao', 'en' => 'Hello'];

        expect(withLocale('it', fn () => $settings->getAuthorBioForCurrentLocale()))->toBe('Ciao')
            ->and(withLocale('en', fn () => $settings->getAuthorBioForCurrentLocale()))->toBe('Hello');
    });

    it('returns null when the bio is missing or blank', function () {
        $settings = resolve(BlogSettings::class);
        $settings->author_bio = ['it' => '  ', 'en' => ''];

        expect(withLocale('it', fn () => $settings->getAuthorBioForCurrentLocale()))->toBeNull()
            ->and(withLocale('en', fn () => $settings->getAuthorBioForCurrentLocale()))->toBeNull()
            ->and(withLocale('de', fn () => $settings->getAuthorBioForCurrentLocale()))->toBeNull();
    });

    it('persists the values through save', function () {
        $settings = resolve(BlogSettings::class);
        $settings->author_bio = ['en' => 'Saved'];
        $settings->save();

        expect(withLocale('en', fn () => resolve(BlogSettings::class)->getAuthorBioForCurrentLocale()))->toBe('Saved');
    });
});
