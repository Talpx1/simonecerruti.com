{{-- Shared "shot" frame + caption chrome for the decorative Area mockups. --}}
@props(['label'])

<div class="relative overflow-hidden aspect-[4/3] border border-light/15 bg-gradient-to-br from-light/5 to-transparent">
    {{ $slot }}

    <span
        class="absolute left-3 bottom-3 z-10 text-[10px] tracking-[0.14em] uppercase text-light/55 bg-dark/40 border border-light/15 px-2.5 py-1 backdrop-blur-sm">{{ $label }}</span>
</div>
