@php
    $email = config('company.contacts.email');
@endphp

<section class="band" aria-label="{{ __('Contacts') }}">
    <h2 class="big" aria-label="{{ __('Let\'s talk.') }}"><span data-split>{{ __('Let\'s talk.') }}</span></h2>

    <div class="band-row">
        <p>
            <b>{{ __('15 minutes. Free.') }}</b>
            {{ __('Tell me what you need, I tell you how I would build it. If I do not convince you, you lost nothing. Write to me at') }}
            <b>{{ $email }}</b>
        </p>

        <div class="actions">
            <button class="ghost-btn" type="button" data-copy="{{ $email }}" data-copied="{{ __('Copied') }}"
                data-pan="cta-band-copy-email">{{ __('Copy email') }}</button>
            <a class="cta xl mag" href="{{ route('contacts') }}" wire:navigate data-cursor="{{ __('Write to me') }}"
                data-pan="cta-band-contacts"><span>{{ __('Write to me') }}</span><span class="ar">→</span></a>
        </div>
    </div>
</section>
