<?php

declare(strict_types=1);

it('redirects the root url to a localized url', function () {
    // The mcamara localization middleware negotiates a locale and redirects
    // the bare root url. Full home-page rendering is covered by the Livewire
    // Home component test.
    $this->get('/')->assertRedirect();
});
