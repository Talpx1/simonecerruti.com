@props(['link' => null])

@php
    $html_tag = $link ? 'a' : 'span';
@endphp

<{{ $html_tag }} @if ($link) href="{{ $link }}" @endif @class([
    'p-1',
    'text-dark',
    'bg-light',
    'text-sm',
    '2xl:text-lg',
    'underline' => $html_tag === 'a',
    'underline-offset-1' => $html_tag === 'a',
])>
    {{ $slot }}
    </{{ $html_tag }}>
