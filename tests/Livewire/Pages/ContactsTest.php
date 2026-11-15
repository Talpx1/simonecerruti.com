<?php

declare(strict_types=1);

use App\Livewire\Pages\Contacts;

use function Pest\Livewire\livewire;

it('renders the hero with the ping effect and the embedded contact form', function (): void {
    livewire(Contacts::class)
        ->assertOk()
        ->assertSeeHtml('data-pan="section-impression-contacts"')
        ->assertSeeHtml('data-effect="contact-network-ping"')
        ->assertSeeHtml('class="contact-layout"')
        ->assertSee(__('Let\'s talk.'))
        ->assertSeeHtml('data-pan="cta-contacts-submit"');
});

it('shows the next-steps list', function (): void {
    livewire(Contacts::class)
        ->assertOk()
        ->assertSee(__('I reply within 24 hours.'))
        ->assertSee(__('Free introductory call.'))
        ->assertSee(__('Tailor-made proposal.'));
});

it('lists the three direct channels built from the company config', function (): void {
    livewire(Contacts::class)
        ->assertOk()
        ->assertSee(__('Book a call'))
        ->assertSee(__('Email me'))
        ->assertSee(__('Text me on WhatsApp'))
        ->assertSeeHtml('href="'.config()->string('company.contacts.koalenda_url').'"')
        ->assertSeeHtml('href="mailto:'.config()->string('company.contacts.email').'"')
        ->assertSeeHtml('https://wa.me/'.config()->string('company.contacts.whatsapp.number'))
        ->assertSeeHtml('data-pan="cta-contacts-call"')
        ->assertSeeHtml('data-pan="cta-contacts-email"')
        ->assertSeeHtml('data-pan="cta-contacts-whatsapp"');
});

it('renders the social grid scoped to the contacts placement', function (string $social): void {
    livewire(Contacts::class)
        ->assertOk()
        ->assertSee(__('Follow me'))
        ->assertSeeHtml('data-pan="cta-social-'.$social.'-contacts"');
})->with(['linkedin', 'instagram', 'github', 'bluesky', 'x']);

it('registers every contacts data-pan in the analytics allowlist', function (): void {
    expect(config()->array('analytics.pan.allowed_analytics'))->toContain(
        'section-impression-contacts',
        'cta-contacts-call',
        'cta-contacts-email',
        'cta-contacts-whatsapp',
        'cta-contacts-submit',
        'cta-social-linkedin-contacts',
        'cta-social-instagram-contacts',
        'cta-social-github-contacts',
        'cta-social-bluesky-contacts',
        'cta-social-x-contacts',
    );
});

it('is reachable at its localized route and emits a meta description', function (): void {
    assertPageEmitsMetaDescription(
        'contacts',
        __('Let\'s talk about your project: tailor-made websites, e-commerce, platforms and management software. Write to me or book a free, no-obligation call.'),
    );
});

it('leaves the page indexable', function (): void {
    assertPageIsIndexable('contacts');
});
