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
    @include('layouts.public.seo', ['seo_data' => $seo_data ?? null])

    @stack('seo')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles
    @vite(['resources/css/app.css', 'resources/js/public.js'])

    <script>
        window.__cookieConsent = {
            given: {{ $cookieConsent['given'] ? 'true' : 'false' }},
            necessary: true,
            analytics: {{ $cookieConsent['analytics'] ? 'true' : 'false' }},
            functional: {{ $cookieConsent['functional'] ? 'true' : 'false' }},
            marketing: {{ $cookieConsent['marketing'] ? 'true' : 'false' }},
        };

        window.__appEnv = '{{ app()->environment() }}';
        window._clarityId = '{{ config('services.clarity.id') }}';
    </script>
</head>

<body>
    @persist('persisted-effect-layers')
        <div id="persisted-effect-layers" aria-hidden="true">
            <div class="effect-vignette"></div>
            <div class="effect-grain"></div>
            <div class="scroll-progress"></div>
            <div class="cursor-ring"><span></span></div>
            <div class="cursor-dot"></div>
        </div>
    @endpersist

    @if (!($canvas ?? false))
        @if ($header ?? true)
            <x-layouts::public.header />
            <x-layouts::public.menu />
        @endif

        <main>
            <x-layouts::public.missing-translations-badge />
            <x-layouts::public.work-in-progress-banner />

            {{ $slot }}

            @if ($footer ?? true)
                <x-layouts::public.footer />
            @endif
        </main>
    @else
        {{ $slot }}
    @endif

    @livewire('notifications')

    @filamentScripts
    <script data-navigate-once>
        loadRecaptcha();
        document.addEventListener('livewire:navigated', loadRecaptcha);

        function loadRecaptcha() {
            // Always clean up any previously injected reCAPTCHA. This also runs
            // when navigating away from the contact form via wire:navigate.
            const existingScript = document.querySelector('script[src*="google.com/recaptcha"]');
            if (existingScript) {
                existingScript.remove();
            }

            const badges = document.querySelectorAll('.grecaptcha-badge');
            badges.forEach(badge => badge.parentElement?.remove());

            const iframes = document.querySelectorAll('iframe[src*="google.com/recaptcha"]');
            iframes.forEach(iframe => iframe.remove());


            if (window.grecaptcha) {
                delete window.grecaptcha;
            }

            // Only load reCAPTCHA on pages that actually use it (the contact form),
            // otherwise it adds third-party noise to every page's console.
            if (!document.querySelector('[data-recaptcha-required]')) {
                return;
            }

            const script = document.createElement('script');
            script.src =
                `{{ config()->string('services.recaptcha.base_url') }}/api.js?hl={{ app()->currentLocale() }}&render={{ config()->string('services.recaptcha.key') }}`;
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        }
    </script>
    @vite('resources/js/cookie-consent.js')
    @stack('scripts')

    <livewire:components.cookie-consent />
</body>

</html>
