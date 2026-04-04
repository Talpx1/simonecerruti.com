@props(['tags', 'tagClasses' => '', 'prefix' => '#'])

<div {{ $attributes->merge(['class' => 'flex gap-4 items-center flex-wrap']) }}>
    @foreach ($tags as $tag => $link)
        @php
            if (is_int($tag)) {
                $tag = $link;
                $link = route('tag_archive', ['slug' => $tag->slug]);
            }
        @endphp
        <x-post-tag class="{{ $tagClasses }}" :$link>{{ $prefix . $tag->name }}</x-post-tag>
    @endforeach
</div>
