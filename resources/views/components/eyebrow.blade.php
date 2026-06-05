@props(['tone' => 'light'])

@php
    $text_color = $tone === 'dark' ? 'text-dark/55' : 'text-light/55';
    $tick_color = $tone === 'dark' ? 'bg-dark' : 'bg-light';
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-3 font-mono text-xs uppercase tracking-[0.22em]', $text_color]) }}>
    <span class="w-[7px] h-[7px] shrink-0 {{ $tick_color }}" aria-hidden="true"></span>
    {{ $slot }}
</span>
