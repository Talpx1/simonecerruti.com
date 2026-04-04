@props(['article', 'index' => null, 'eager' => false, 'showTags' => true])

<article class="group relative flex flex-col overflow-hidden border border-light/10">

    @if ($article->featured_image_url)
        <div class="overflow-hidden h-48 lg:h-56 border-b border-light/10 relative">
            <picture>
                @if ($article->getFirstMedia('featured_image')?->hasGeneratedConversion('featured_image_webp'))
                    <source srcset="{{ $article->getFirstMediaUrl('featured_image', 'featured_image_webp') }}"
                        type="image/webp">
                @endif
                <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}"
                    loading="{{ $eager || ($index !== null && $index < 3) ? 'eager' : 'lazy' }}"
                    class="w-full h-full object-cover object-center saturate-0 brightness-75 group-hover:saturate-50 group-hover:brightness-90 transition-all duration-500">
            </picture>

            @php
                $chips = $article->tags->where('type', \App\Enums\TagTypes::BLOG_CATEGORY->value)->pluck('name');

                if ($article->featured) {
                    $chips->merge([__('Featured')]);
                }
            @endphp
            @if ($chips->isNotEmpty())
                <x-chip-list class="absolute top-4 left-4" :entries="$chips" />
            @endif
        </div>
    @endif

    <div class="flex flex-col justify-between gap-6 p-8 grow">
        <div class="space-y-4">
            <div class="flex items-center gap-4 text-light/25 text-xs font-mono tracking-widest">
                @if ($article->published_at)
                    <time datetime="{{ $article->published_at->toDateString() }}">
                        {{ $article->published_at->format('d.m.Y') }}
                    </time>
                @endif
            </div>

            <h2 class="font-black uppercase leading-none tracking-tighter text-light text-2xl lg:text-3xl">
                {{ $article->title }}
            </h2>

            <p class="text-light/50 text-sm leading-relaxed font-light line-clamp-3">
                {{ $article->summary }}
            </p>
        </div>

        <a wire:navigate href="{{ route('blog_article.show', $article->slug) }}"
            class="w-fit group/link relative overflow-hidden border border-light/20 text-light/50 flex items-center gap-3 px-5 py-3 hover:text-dark hover:border-light transition-colors duration-300">
            <span
                class="absolute inset-0 bg-light translate-y-full group-hover/link:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
            <span class="relative z-10 text-xs font-semibold uppercase tracking-widest">{{ __('Read') }}</span>
            <x-ri-arrow-right-long-line class="relative z-10 w-3.5" />
        </a>

        @php
            $tags = $article->tags->where('type', \App\Enums\TagTypes::TAG->value);
        @endphp
        @if ($showTags && $tags->isNotEmpty())
            <x-post-tag-list :tags="$tags" />
        @endif
    </div>
</article>
