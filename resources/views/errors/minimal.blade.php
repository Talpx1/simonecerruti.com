@php
    $home_url = route('home');
    $can_use_vite = file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json'));
    $rows = explode('|', trim($__env->yieldContent('message')));
    $page_title = trim($__env->yieldContent('title'));
    $links = [
        [__('Works'), route('projects')],
        [__('Method'), route('how_i_work')],
        [__('Services'), route('services')],
        [__('About'), route('about')],
        [__('Blog'), route('blog')],
        [__('Contacts'), route('contacts')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <title>{!! $page_title !!} · {{ config('company.friendly_name') }}</title>

    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
    <link rel="shortcut icon" href="/favicon/favicon.ico" />

    @if ($can_use_vite)
        @vite(['resources/css/app.css', 'resources/js/public.js'])
    @endif
</head>

<body>
    <div id="persisted-effect-layers" aria-hidden="true">
        <div class="effect-vignette"></div>
        <div class="effect-grain"></div>
        <div class="scroll-progress"></div>
        <div class="cursor-ring"><span></span></div>
        <div class="cursor-dot"></div>
    </div>

    <header class="site-nav">
        <div class="site-nav-inner">
            <a class="site-brand" href="{{ $home_url }}" aria-label="{{ config('company.friendly_name') }}, {{ __('Home') }}">
                <x-app-logo class="site-brand-logo" width="52" height="28" color="currentColor" aria-hidden="true" />
                <b>{{ config('company.friendly_name') }}</b><i>{{ __('tailor-made') }}</i>
            </a>
        </div>
    </header>

    <main>
        <section class="page-hero">
            <canvas id="not-found-canvas" class="page-hero-background" data-effect="not-found-particles" data-code="@yield('code')" aria-hidden="true"></canvas>

            <h1 class="page-hero-title" aria-label="{!! str_replace('|', ' ', $page_title) !!}">
                @foreach ($rows as $row)
                    <span class="page-hero-title-line" data-variable-letters>{!! $row !!}</span>
                @endforeach
            </h1>

            <div class="page-hero-subtitle">
                @hasSection('description')
                    <p>@yield('description')</p>
                @endif

                <div class="button-group">
                    <a class="button-outline-link" href="{{ $home_url }}">{{ __('Back to home') }} <span>→</span></a>
                    @hasSection('cta')
                        <a class="button-primary button-large magnetic-hover home-hero-cta" href="{{ route('contacts') }}"><span>@yield('cta')</span><span class="button-arrow">→</span></a>
                    @endif
                </div>
            </div>
        </section>

        <section class="section section--tight">
            <nav class="tag-cloud" aria-label="{{ __('Maybe you were looking for') }}">
                @foreach ($links as [$label, $url])
                    <a href="{{ $url }}">{{ $label }}</a>
                @endforeach
            </nav>
        </section>
    </main>
</body>

</html>
