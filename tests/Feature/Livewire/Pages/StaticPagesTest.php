<?php

declare(strict_types=1);

use App\Livewire\Pages\About;
use App\Livewire\Pages\Contacts;
use App\Livewire\Pages\CookiePolicy;
use App\Livewire\Pages\HowIWork;
use App\Livewire\Pages\PrivacyPolicy;
use App\Livewire\Pages\TermsAndConditions;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->withoutVite());

it('renders the static pages', function (string $component) {
    livewire($component)->assertOk();
})->with([
    'about' => [About::class],
    'how I work' => [HowIWork::class],
    'contacts' => [Contacts::class],
    'cookie policy' => [CookiePolicy::class],
    'privacy policy' => [PrivacyPolicy::class],
    'terms and conditions' => [TermsAndConditions::class],
]);
