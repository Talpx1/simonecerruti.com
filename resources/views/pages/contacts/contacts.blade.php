@php
    $steps = [
        [__('I reply within 24 hours.'), __('I read it carefully and get back to you in person, no auto-responders.')],
        [__('Free introductory call.'), __('15 minutes to understand goals and priorities, no strings attached.')],
        [__('Tailor-made proposal.'), __('A concrete estimate, built around your project and not off a price list.')],
    ];

    $email = config()->string('company.contacts.email');

    $whatsapp_url = sprintf(
        'https://wa.me/%s?text=%s',
        config()->string('company.contacts.whatsapp.number'),
        rawurlencode(__(config()->string('company.contacts.whatsapp.default_message'))),
    );
@endphp

<div>
    <x-page-hero fx="ping" :label="__('Contacts: let\'s talk.')" :rows="[__('Let\'s talk.')]"
        data-pan="section-impression-contacts" :data-ping-label="__('SIMONE · REPLIES WITHIN 24 HOURS')">
        {!! __('I reply within 24 hours, in person. Let\'s start here: <em>we build the rest together.</em>') !!}
    </x-page-hero>

    <section class="sec tight">
        <div class="ct-grid">
            <div>
                <livewire:components.contact-form />
            </div>

            <div>
                <div class="steps">
                    @foreach ($steps as $i => [$step_title, $step_body])
                        <div class="step">
                            <b>{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</b>
                            <div>
                                <h3>{{ $step_title }}</h3>
                                <p>{{ $step_body }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="direct">
                    <a class="dc dc-call mag" href="{{ config()->string('company.contacts.koalenda_url') }}"
                        data-cursor="{{ __('Book a call') }}" target="_blank" rel="noopener"
                        data-pan="cta-contacts-call">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="16" />
                            <path d="M3 10h18M8 3v4M16 3v4M8 14h3v3H8z" />
                        </svg>
                        <span><b>{{ __('Book a call') }}</b><small>{{ __('Rather talk than write? Pick a slot and we\'ll have a chat.') }}</small></span>
                        <i>→</i>
                    </a>
                    <a class="dc dc-wa mag" href="{{ $whatsapp_url }}" data-cursor="{{ __('Start a Chat') }}"
                        target="_blank" rel="noopener" data-pan="cta-contacts-whatsapp">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 20l1.2-4A8 8 0 1 1 8 18.8z" />
                            <path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1-1.5-2-1-1 .8a4 4 0 0 1-1.8-1.8l.8-1-1-2z" />
                        </svg>
                        <span><b>{{ __('Text me on WhatsApp') }}</b><small>{{ __('A quick question or an informal first contact? Send me a message.') }}</small></span>
                        <i>→</i>
                    </a>
                    <a class="dc dc-wa mag" href="mailto:{{ $email }}" data-cursor="{{ __('Email me') }}"
                        data-pan="cta-contacts-email">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" />
                            <path d="M3 7l9 7 9-7" />
                        </svg>
                        <span><b>{{ __('Email me') }}</b><small>{{ $email }}</small></span>
                        <i>→</i>
                    </a>
                </div>

                <div class="ct-soc">
                    <span>{{ __('Follow me') }}</span>
                    <x-social-links variant="foot" placement="contacts" />
                </div>
            </div>
        </div>
    </section>
</div>
