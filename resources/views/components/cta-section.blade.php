@props(['centered' => false])

{{-- Shared closing call-to-action band: a headline (+ optional muted second
     line) above a row of <x-button> actions. Replaces the near-identical
     section that was duplicated across the projects, blog and how-i-work
     pages. Pass the headline via the <x-slot:title> / <x-slot:subtitle>
     slots and the buttons as the default slot. --}}
<section {{ $attributes->class('bg-dark border-t border-light/10 px-8 lg:px-14 py-24 lg:py-36') }}>
    <div @class(['max-w-5xl', 'mx-auto' => $centered])>
        <p data-reveal class="font-semibold leading-tight tracking-tight text-light text-3xl lg:text-5xl 2xl:text-6xl mb-16">
            {{ $title }}@isset($subtitle)<br><span class="text-light/25">{{ $subtitle }}</span>@endisset
        </p>

        <div data-reveal class="flex flex-col sm:flex-row gap-4">
            {{ $slot }}
        </div>
    </div>
</section>
