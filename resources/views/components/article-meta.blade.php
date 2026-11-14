@props(['article', 'short' => false, 'withAuthor' => false])

@php
    $category = $article->tags->firstWhere('type', \App\Enums\TagTypes::BLOG_CATEGORY->value);
@endphp

<div {{ $attributes->class('pmeta') }}>
    @if ($withAuthor)
        <span class="pm-a"><i aria-hidden="true">{{ $article->author->initials }}</i>{{ $article->author->name }}</span>
    @endif
    @if ($article->published_at)
        <time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->translatedFormat('j M Y') }}</time>
    @endif
    <span>{{ $short ? __(':minutes min', ['minutes' => $article->reading_time]) : __(':minutes min read', ['minutes' => $article->reading_time]) }}</span>
    @if ($category)
        <span class="pm-tag">{{ $category->name }}</span>
    @endif
</div>
