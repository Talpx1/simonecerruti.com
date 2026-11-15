@php
    $nav_links = [
        ['projects', __('Works'), route('projects')],
        ['how_i_work', __('Method'), route('how_i_work')],
        ['services', __('Services'), route('services')],
        ['about', __('About'), route('about')],
        ['blog', __('Blog'), route('blog')],
        ['contacts', __('Contacts'), route('contacts')],
    ];
@endphp

<header class="site-nav">
    <div class="site-nav-inner">
        <a class="site-brand" href="{{ route('home') }}" wire:navigate data-pan="cta-nav-home"
            aria-label="{{ config('company.friendly_name') }}, {{ __('Home') }}">
            <x-app-logo class="site-brand-logo" width="52" height="28" color="currentColor" aria-hidden="true" />
            <b>{{ config('company.friendly_name') }}</b><i>{{ __('tailor-made') }}</i>
        </a>

        <nav class="site-nav-links" aria-label="{{ __('Main') }}">
            @foreach ($nav_links as [$slug, $label, $url])
                <a href="{{ $url }}" wire:navigate data-pan="cta-nav-{{ $slug }}" @if (str_starts_with(request()->url(), $url)) class="is-current" aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>

        <div class="site-nav-language" role="group" aria-label="{{ __('Language') }}">
            @foreach (App::supportedLocales() as $locale_code => $properties)
                <a href="{{ Route::localizedUrl(locale: $locale_code, force_default_location: true) }}"
                    lang="{{ $locale_code }}" hreflang="{{ $locale_code }}" aria-label="{{ $properties['native'] }}"
                    @if (App::getLocale() === $locale_code) aria-current="true" @endif>{{ str($locale_code)->upper() }}</a>
            @endforeach
        </div>
    </div>

    <a class="button-primary magnetic-hover site-nav-cta" href="{{ route('contacts') }}" wire:navigate data-pan="cta-nav-cta" tabindex="-1"
        aria-hidden="true"><span>{{ __('Let\'s talk') }}</span><span class="button-arrow">→</span></a>

    <button class="site-nav-toggle" type="button" id="mobile-menu-toggle" aria-expanded="false" aria-controls="mobile-menu"
        data-label-open="{{ __('Menu') }}" data-label-close="{{ __('Close') }}">{{ __('Menu') }}</button>
</header>
