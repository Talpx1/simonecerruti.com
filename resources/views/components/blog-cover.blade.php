@props(['article', 'class' => 'pcover'])

<span {{ $attributes->class($class) }} aria-hidden="true">
    @if ($article->hasMedia('featured_image'))
        <img src="{{ $article->featured_image_url }}" alt="" loading="lazy">
    @endif
    <i>{{ mb_strtoupper(mb_substr($article->title, 0, 1)) }}</i>
</span>
