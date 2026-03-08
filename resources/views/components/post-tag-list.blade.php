@props(['tags'])

<div {{ $attributes->merge(['class' => 'flex gap-4 items-center flex-wrap']) }}>
    @foreach ($tags as $tag => $link)
        @php
            if (is_int($tag)) {
                $tag = $link;
                $link = null;
            }
        @endphp
        <x-post-tag :$link>{{ $tag }}</x-post-tag>
    @endforeach
</div>
