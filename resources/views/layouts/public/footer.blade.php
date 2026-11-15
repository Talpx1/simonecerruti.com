@php
    $foot_links = [
        ['about', __('About'), route('about')],
        ['services', __('Services'), route('services')],
        ['projects', __('Works'), route('projects')],
        ['how_i_work', __('How I work'), route('how_i_work')],
        ['blog', __('Blog'), route('blog')],
        ['contacts', __('Contacts'), route('contacts')],
    ];
@endphp

<footer class="site-footer">
    <div class="site-footer-top">
        <nav class="site-footer-pages" aria-label="{{ __('Pages') }}">
            @foreach ($foot_links as [$slug, $label, $url])
                <a href="{{ $url }}" wire:navigate data-pan="cta-footer-{{ $slug }}">{{ $label }}</a>
            @endforeach
        </nav>

        <x-social-links variant="foot" placement="footer" />
    </div>

    <div class="site-footer-legal">
        <span>© {{ date('Y') }} {{ config('company.friendly_name') }} · {{ __('VAT') }} {{ config('company.vat') }}</span>

        <nav aria-label="{{ __('Legal notes') }}">
            <a href="{{ route('privacy_policy') }}" wire:navigate>{{ __('Privacy') }}</a>
            <a href="{{ route('cookie_policy') }}" wire:navigate>{{ __('Cookies') }}</a>
            <a href="{{ route('terms_and_conditions') }}" wire:navigate>{{ __('Terms') }}</a>
            <button type="button" class="button-link" data-cookie-prefs onclick="Livewire.dispatch('open-cookie-banner')">
                {{ __('Cookie preferences') }}
            </button>
        </nav>
    </div>
</footer>
