<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
    <link rel="shortcut icon" href="/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}" />
    <link rel="manifest" href="/favicon/site.webmanifest" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>
        {{ __($title) }}

        @if (!isset($suffix))
            |&nbsp;{{ config('app.name') }}
        @elseif ($suffix !== false)
            |&nbsp;{{ __($suffix) }}
        @endif
    </title>

    <link rel="canonical" href="{{ url()->current() }}">
    @foreach (app()->supportedLocales() as $locale => $props)
        <link rel="alternate" hreflang="{{ $locale }}"
            href="{{ Route::localizedUrl(locale: $locale, force_default_location: true) }}">
    @endforeach

    @stack('seo')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles
    @vite(['resources/css/app.css', 'resources/js/public.js'])
</head>

<body class="antialiased bg-dark text-light min-h-screen grid grid-cols-1 grid-rows-[auto_1fr_auto]"
    x-data="{ 'isMenuOpen': false }" x-on:keydown.escape="isMenuOpen=false">
    @if (!($canvas ?? false))
        @if ($header ?? true)
            <x-layouts.public.menu />
            <x-layouts.public.header />
        @endif

        <main>
            <x-layouts.public.missing-translations-badge />

            {{ $slot }}
        </main>

        @if ($footer ?? true)
            <x-layouts.public.footer />
        @endif
    @else
        {{ $slot }}
    @endif

    @livewire('notifications')


    @filamentScripts
    <script>
        document.addEventListener('livewire:navigated', () => {
            window.listenersToRemove = []
        }, {
            once: true
        })

        document.addEventListener('livewire:navigating', () => {
            gsapScrollTrigger.getAll().forEach(t => t.kill());
            gsap.globalTimeline.clear();

            window.listenersToRemove.forEach(l => {
                document.removeEventListener(l[0], l[1])
            })
            window.listenersToRemove = []
        }, {
            once: true
        })

        loadRecaptcha();
        document.addEventListener('livewire:navigated', loadRecaptcha);

        function loadRecaptcha() {
            const existingScript = document.querySelector('script[src*="google.com/recaptcha"]');
            if (existingScript) {
                existingScript.remove();
            }

            if (window.grecaptcha) {
                delete window.grecaptcha;
            }

            const script = document.createElement('script');
            script.src =
                `{{ config()->string('services.recaptcha.base_url') }}/api.js?hl={{ app()->currentLocale() }}&render={{ config()->string('services.recaptcha.key') }}`;
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        }
    </script>
    @stack('scripts')
</body>

</html>
