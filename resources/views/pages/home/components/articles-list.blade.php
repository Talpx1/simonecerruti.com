@props(['articles', 'seeAllRoute'])

<div class="flex flex-col">
    <div
        class="p-4 bg-light lg:bg-transparent lg:bg-linear-[to_right,var(--color-light),transparent] flex flex-col lg:flex-row items-center justify-between">
        <h3 class="text-2xl 2xl:text-4xl uppercase text-dark">
            {{ $heading }}
        </h3>

        <a href="{{ $seeAllRoute }}" wire:navigate
            class="text-nowrap mt-2 lg:mt-0 text-dark lg:text-light lg:text-lg 2xl:text-2xl underline decoration-2 underline-offset-4 uppercase flex items-center gap-1">
            {{ __('See all') }} <x-ri-arrow-right-long-line class="w-6" />
        </a>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-8 p-4 2xl:p-8 h-full">
        @foreach ($articles as $article)
            <article class="group relative flex flex-col overflow-hidden border border-light/10">
                @if ($article->featured_image_url)
                    <div class="overflow-hidden w-full h-full object-cover object-center absolute top-0 left-0">
                        <picture>
                            @if ($article->getFirstMedia('featured_image')?->hasGeneratedConversion('featured_image_webp'))
                                <source
                                    srcset="{{ $article->getFirstMediaUrl('featured_image', 'featured_image_webp') }}"
                                    type="image/webp">
                            @endif
                            <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}" loading="lazy"
                                class="w-full h-full object-cover object-center saturate-0 brightness-50 group-hover:saturate-50 group-hover:brightness-90 transition-all duration-500">
                        </picture>
                    </div>
                @endif

                <div class="flex flex-col justify-between gap-6 p-4 xl:p-8 2xl:p-16 grow z-10">
                    <div class="space-y-2 xl:space-y-4">
                        <div class="flex items-center justify-between">
                            @if ($article->published_at)
                                <div class="block text-light/25 text-xs font-mono tracking-widest">
                                    <time datetime="{{ $article->published_at->toDateString() }}">
                                        {{ $article->published_at->format('d.m.Y') }}
                                    </time>
                                </div>
                            @endif

                            @if ($article->featured)
                                <x-chip>{{ __('Featured') }}</x-chip>
                            @endif
                        </div>

                        <h2 class="font-black uppercase leading-none tracking-tighter text-light text-2xl lg:text-3xl">
                            {{ $article->title }}
                        </h2>

                        <p class="text-light/50 text-sm 2xl:text-[16px] leading-relaxed font-light line-clamp-3">
                            {{ $article->summary }}
                        </p>
                    </div>

                    <a wire:navigate href="{{ route('blog_article.show', $article->slug) }}"
                        class="w-fit group/link relative overflow-hidden border border-light/20 text-light/50 flex items-center gap-3 px-5 py-3 hover:text-dark hover:border-light transition-colors duration-300">
                        <span
                            class="absolute inset-0 bg-light translate-y-full group-hover/link:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
                        <span
                            class="relative z-10 text-xs font-semibold uppercase tracking-widest">{{ __('Read') }}</span>
                        <x-ri-arrow-right-long-line class="relative z-10 w-3.5" />
                    </a>

                    @if ($article->tags->isNotEmpty())
                        <div class="hidden 2xl:block">
                            <x-post-tag-list :tags="$article->tags" />
                        </div>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
</div>
