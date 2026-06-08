@props([
    'href',
    'prefix',
    'label',
    'pan' => null,
])

<a wire:navigate href="{{ $href }}" @if ($pan) data-pan="{{ $pan }}" @endif
    class="group/cta inline-flex items-center gap-2 text-xs uppercase tracking-[0.08em] text-light/55 hover:text-light transition-colors">
    {{ $prefix }} <u class="underline-offset-4">{{ $label }}</u>
    <x-ri-arrow-right-long-line class="w-3.5 shrink-0 transition-transform group-hover/cta:translate-x-1" />
</a>
