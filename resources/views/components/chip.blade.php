@props([
    'size' => '2xs',
])

<span
    {{ $attributes->class([
        'tracking-widest',
        'border',
        'border-light/20',
        'px-2.5',
        'py-1',
        'text-light/40',
        'uppercase',
        'text-[10px]' => $size === '2xs',
        "text-{$size}" => $size !== '2xs',
    ]) }}>
    {{ $slot }}
</span>
