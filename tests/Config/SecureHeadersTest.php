<?php

declare(strict_types=1);

it('sends the baseline security headers on web responses', function (): void {
    $this->get(route('contacts'))
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'sameorigin')
        ->assertHeaderMissing('Content-Security-Policy');
});
