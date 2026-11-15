@php
    $menu_links = [
        ['home', __('Home'), route('home')],
        ['projects', __('Works'), route('projects')],
        ['how_i_work', __('Method'), route('how_i_work')],
        ['services', __('Services'), route('services')],
        ['about', __('About'), route('about')],
        ['blog', __('Blog'), route('blog')],
        ['contacts', __('Contacts'), route('contacts')],
    ];
@endphp

<nav class="mobile-menu" id="mobile-menu" aria-label="{{ __('Menu') }}">
    @foreach ($menu_links as [$slug, $label, $url])
        <a href="{{ $url }}" wire:navigate data-pan="cta-nav-{{ $slug }}-mobile">{{ $label }}</a>
    @endforeach
    <a class="text-accent" href="{{ route('contacts') }}" wire:navigate data-pan="cta-nav-cta-mobile">{{ __('Let\'s talk') }} →</a>

    <div class="mobile-menu-language">
        @foreach (App::supportedLocales() as $locale_code => $properties)
            <a href="{{ Route::localizedUrl(locale: $locale_code, force_default_location: true) }}"
                lang="{{ $locale_code }}" hreflang="{{ $locale_code }}"
                @if (App::getLocale() === $locale_code) aria-current="true" @endif>{{ $properties['native'] }}</a>
        @endforeach
    </div>
</nav>
