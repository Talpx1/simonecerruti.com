@props([
    'href' => '#',
    'variant' => 'primary',
    'navigate' => true,
    'external' => false,
])

@php
    $variant_classes = $variant === 'secondary'
        ? 'border-light/20 text-light/50 hover:border-light'
        : 'border-light text-light';
@endphp

<a href="{{ $href }}"
    @if ($external) target="_blank" rel="noopener" @elseif ($navigate) wire:navigate @endif
    {{ $attributes->class([
        'group relative overflow-hidden border flex items-center justify-between gap-8 px-8 py-5 hover:text-dark transition-colors duration-300',
        $variant_classes,
    ]) }}>
    <span
        class="absolute inset-0 bg-light translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
    <span class="relative z-10 font-semibold uppercase tracking-widest text-sm">{{ $slot }}</span>
    <x-ri-arrow-right-long-line class="relative z-10 w-5 shrink-0" />
</a>
