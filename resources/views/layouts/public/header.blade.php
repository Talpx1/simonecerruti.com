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

<header class="nav">
    <div class="blend">
        <a class="mark" href="{{ route('home') }}" wire:navigate data-pan="cta-nav-home"
            aria-label="{{ config('company.friendly_name') }}, {{ __('Home') }}">
            <x-app-logo class="logo" width="52" height="28" color="currentColor" aria-hidden="true" />
            <b>{{ config('company.friendly_name') }}</b><i>{{ __('tailor-made') }}</i>
        </a>

        <nav class="links" aria-label="{{ __('Main') }}">
            @foreach ($nav_links as [$slug, $label, $url])
                <a href="{{ $url }}" wire:navigate data-pan="cta-nav-{{ $slug }}" @if (str_starts_with(request()->url(), $url)) class="here" aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>

        <div class="lang" role="group" aria-label="{{ __('Language') }}">
            @foreach (App::supportedLocales() as $locale_code => $properties)
                <a href="{{ Route::localizedUrl(locale: $locale_code, force_default_location: true) }}"
                    lang="{{ $locale_code }}" hreflang="{{ $locale_code }}" aria-label="{{ $properties['native'] }}"
                    @if (App::getLocale() === $locale_code) aria-current="true" @endif>{{ str($locale_code)->upper() }}</a>
            @endforeach
        </div>
    </div>

    <a class="cta mag nav-cta" href="{{ route('contacts') }}" wire:navigate data-pan="cta-nav-cta" tabindex="-1"
        aria-hidden="true"><span>{{ __('Let\'s talk') }}</span><span class="ar">→</span></a>

    <button class="burger" type="button" id="burger" aria-expanded="false" aria-controls="mmenu"
        data-label-open="{{ __('Menu') }}" data-label-close="{{ __('Close') }}">{{ __('Menu') }}</button>
</header>
