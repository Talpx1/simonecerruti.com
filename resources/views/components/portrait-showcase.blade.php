@props([
    'alternativeText',
])

<div {{ $attributes->class(['portrait-showcase'])->merge(['data-effect' => 'portrait-showcase']) }}>
    <svg class="portrait-showcase-logo-wave" viewBox="0 0 100 54" aria-hidden="true">
        <path d="M3 51.4989C24 51.4989 26.0781 44.3738 36.5 29.9988C51 9.99881 74 -6.50109 97 9.99882C74 9.99882 74 28.4987 74 29.9987C74 31.4987 74 51.4987 97 51.4987"/>
    </svg>
    <div class="portrait-showcase-photo-card">
        <picture>
            <source type="image/webp"
                srcset="{{ asset('images/simone-cerruti-600.webp') }} 600w, {{ asset('images/simone-cerruti.webp') }} 1200w"
                sizes="(max-width: 900px) 90vw, 500px">
            <img src="{{ asset('images/simone-cerruti.jpg') }}" alt="{{ $alternativeText }}" width="1200" height="1500"
                loading="lazy" decoding="async">
        </picture>
        <span class="portrait-showcase-cursor-glow" aria-hidden="true"></span>
    </div>
</div>
