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
    <div id="fx-layers" aria-hidden="true">
        <div class="vignette"></div>
        <div class="grain"></div>
        <div class="bar"></div>
        <div class="cur"><span></span></div>
        <div class="cur-dot"></div>
    </div>

    <header class="nav">
        <div class="blend">
            <a class="mark" href="{{ $home_url }}" aria-label="{{ config('company.friendly_name') }}, {{ __('Home') }}">
                <x-app-logo class="logo" width="52" height="28" color="currentColor" aria-hidden="true" />
                <b>{{ config('company.friendly_name') }}</b><i>{{ __('tailor-made') }}</i>
            </a>
        </div>
    </header>

    <main>
        <section class="page-hero">
            <canvas id="nf" class="hero-bg nf-bg" data-fx="nf" data-code="@yield('code')" aria-hidden="true"></canvas>

            <h1 class="ph1" aria-label="{!! str_replace('|', ' ', $page_title) !!}">
                @foreach ($rows as $row)
                    <span class="row" data-split>{!! $row !!}</span>
                @endforeach
            </h1>

            <div class="ph-sub">
                @hasSection('description')
                    <p>@yield('description')</p>
                @endif

                <div class="actions">
                    <a class="more" href="{{ $home_url }}">{{ __('Back to home') }} <span>→</span></a>
                    @hasSection('cta')
                        <a class="cta xl mag hero-cta" href="{{ route('contacts') }}"><span>@yield('cta')</span><span class="ar">→</span></a>
                    @endif
                </div>
            </div>
        </section>

        <section class="sec tight">
            <nav class="tagcloud" aria-label="{{ __('Maybe you were looking for') }}">
                @foreach ($links as [$label, $url])
                    <a href="{{ $url }}">{{ $label }}</a>
                @endforeach
            </nav>
        </section>
    </main>
</body>

</html>
