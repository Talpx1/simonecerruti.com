<div>
    <section class="flex flex-col px-8 lg:px-14 pb-20 pt-16 border-b border-light/10">
        <div class="flex items-end justify-between gap-8">
            <h1 class="font-black uppercase leading-none tracking-tight text-7xl lg:text-8xl xl:text-9xl text-light/20">
                {{ __('Blog') }}
            </h1>

            @if ($this->isLoadMoreNotEmpty('articles'))
                <span class="text-light/25 font-mono text-sm tracking-widest mb-2 shrink-0">
                    {{ str_pad((string) $this->getLoadMoreTotal('articles'), 2, '0', STR_PAD_LEFT) }}
                </span>
            @endif
        </div>

        <p class="mt-10 text-light/60 text-lg lg:text-xl font-light leading-relaxed max-w-2xl">
            {{ __('Thoughts, case studies and technical writing on software, design and the craft of building things that last.') }}
        </p>

        @if ($this->categories->isNotEmpty())
            <div class="mt-10 flex flex-wrap gap-2">
                <button wire:click="filterByCategory(null)" @class([
                    'px-4 py-2 text-xs font-semibold uppercase tracking-widest border transition-colors duration-200',
                    'border-light text-dark bg-light' => $this->active_category === null,
                    'border-light/20 text-light/40 hover:border-light/40 hover:text-light/70' =>
                        $this->active_category !== null,
                ])>
                    {{ __('All') }}
                </button>

                @foreach ($this->categories as $category)
                    <button wire:click="filterByCategory('{{ $category->slug }}')" @class([
                        'px-4 py-2 text-xs font-semibold uppercase tracking-widest border transition-colors duration-200',
                        'border-light text-dark bg-light' =>
                            $this->active_category === $category->slug,
                        'border-light/20 text-light/40 hover:border-light/40 hover:text-light/70' =>
                            $this->active_category !== $category->slug,
                    ])>
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        @endif
    </section>

    @if ($this->featured && $active_category === null)
        <section class="border-b border-light/10 grid grid-cols-1 lg:grid-cols-2">
            @if ($this->featured->featured_image_url)
                <div class="h-64 lg:h-auto overflow-hidden border-b lg:border-b-0 lg:border-r border-light/10 relative">
                    <picture>
                        @if ($this->featured->getFirstMedia('featured_image')?->hasGeneratedConversion('featured_image_webp'))
                            <source
                                srcset="{{ $this->featured->getFirstMediaUrl('featured_image', 'featured_image_webp') }}"
                                type="image/webp">
                        @endif
                        <img src="{{ $this->featured->featured_image_url }}" alt="{{ $this->featured->title }}"
                            loading="eager" class="w-full h-full object-cover object-center saturate-0 brightness-60">
                    </picture>
                </div>
            @endif

            <div class="flex flex-col justify-between gap-8 p-8 lg:p-14">
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <span class="text-light/25 text-xs font-mono tracking-widest">
                            @if ($this->featured->published_at)
                                {{ $this->featured->published_at->format('d.m.Y') }}
                            @endif
                        </span>

                        @php
                            $chips = $this->featured->tags
                                ->where('type', \App\Enums\TagTypes::BLOG_CATEGORY->value)
                                ->pluck('name')
                                ->merge([__('Featured')]);
                        @endphp
                        @if ($chips->isNotEmpty())
                            <x-chip-list :entries="$chips" />
                        @endif
                    </div>

                    <h2 class="font-black uppercase leading-none tracking-tighter text-light text-3xl lg:text-5xl">
                        {{ $this->featured->title }}
                    </h2>

                    <p class="text-light/55 text-base font-light leading-relaxed">{{ $this->featured->summary }}</p>


                    @if ($this->featured->tags->where('type', \App\Enums\TagTypes::TAG->value)->isNotEmpty())
                        <x-post-tag-list :tags="$this->featured->tags->where('type', \App\Enums\TagTypes::TAG->value)" />
                    @endif
                </div>

                <a wire:navigate href="{{ route('blog_article.show', $this->featured->slug) }}"
                    class="w-fit group relative overflow-hidden border border-light text-light flex items-center gap-4 px-8 py-4 hover:text-dark transition-colors duration-300">
                    <span
                        class="absolute inset-0 bg-light translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
                    <span
                        class="relative font-semibold uppercase tracking-widest text-sm z-10">{{ __('Read') }}</span>
                    <x-ri-arrow-right-long-line class="z-10 w-5" />
                </a>
            </div>
        </section>
    @endif

    <section>
        @if ($this->isLoadMoreEmpty('articles'))
            <div class="px-8 lg:px-14 py-36 text-light/25 text-sm uppercase tracking-widest font-semibold">
                {{ __('No articles yet.') }}
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 [&>*]:-mt-px [&>*]:-ml-px pl-px pt-px">
                @foreach ($this->getLoadMoreData('articles') as $article)
                    <x-blog-article-card :article="$article" :index="$loop->index" />
                @endforeach
            </div>

            @if ($this->canLoadMore('articles'))
                <div class="border-t border-light/10 px-8 lg:px-14 py-12 flex items-center justify-between">
                    <span class="text-light/25 text-sm font-mono tracking-widest">
                        {{ count($this->getLoadMoreData('articles')) }} / {{ $this->getLoadMoreTotal('articles') }}
                    </span>

                    <button wire:click="loadMore('articles')" wire:loading.attr="disabled"
                        class="group relative overflow-hidden border border-light/20 text-light/50 flex items-center gap-4 px-8 py-4 hover:text-dark hover:border-light transition-colors duration-300">
                        <span
                            class="absolute inset-0 bg-light translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
                        <span class="relative z-10 font-semibold uppercase tracking-widest text-sm">
                            <span wire:loading.remove wire:target="loadMore('articles')">{{ __('Load more') }}</span>
                            <span wire:loading wire:target="loadMore('articles')">{{ __('Loading...') }}</span>
                        </span>
                        <x-ri-arrow-down-long-line class="relative z-10 w-4" wire:loading.remove
                            wire:target="loadMore('articles')" />
                    </button>
                </div>
            @endif
        @endif
    </section>
</div>
