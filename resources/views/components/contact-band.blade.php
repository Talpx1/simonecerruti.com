@php
    $email = config('company.contacts.email');
@endphp

<section class="contact-band" aria-label="{{ __('Contacts') }}">
    <h2 class="contact-band-title" aria-label="{{ __('Let\'s talk.') }}"><span data-variable-letters>{{ __('Let\'s talk.') }}</span></h2>

    <div class="contact-band-row">
        <p>
            <b>{{ __('15 minutes. Free.') }}</b>
            {{ __('Tell me what you need, I tell you how I would build it. If I do not convince you, you lost nothing. Write to me at') }}
            <b>{{ $email }}</b>
        </p>

        <div class="button-group">
            <button class="button-ghost" type="button" data-copy="{{ $email }}" data-copied="{{ __('Copied') }}"
                data-pan="cta-band-copy-email">{{ __('Copy email') }}</button>
            <a class="button-primary button-large magnetic-hover" href="{{ route('contacts') }}" wire:navigate data-cursor="{{ __('Write to me') }}"
                data-pan="cta-band-contacts"><span>{{ __('Write to me') }}</span><span class="button-arrow">→</span></a>
        </div>
    </div>
</section>
