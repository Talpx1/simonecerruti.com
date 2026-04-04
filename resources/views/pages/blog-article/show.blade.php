<div class="-mt-[100px]">
    <section class="border-b border-light/10">
        @if ($blog_article->featured_image_url)
            <div class="h-64 lg:h-96 overflow-hidden relative">
                <picture>
                    @if ($blog_article->getFirstMedia('featured_image')?->hasGeneratedConversion('featured_image_webp'))
                        <source srcset="{{ $blog_article->getFirstMediaUrl('featured_image', 'featured_image_webp') }}"
                            type="image/webp">
                    @endif
                    <img src="{{ $blog_article->featured_image_url }}" alt="{{ $blog_article->title }}" loading="eager"
                        class="w-full h-full object-cover object-center saturate-0 brightness-50">
                </picture>
                <div class="absolute inset-0 bg-gradient-to-t from-dark/80 to-transparent"></div>
            </div>
        @endif

        <div class="px-8 lg:px-14 py-16 lg:py-24 space-y-8">

            <a wire:navigate href="{{ route('blog') }}"
                class="group inline-flex items-center gap-3 text-light/40 hover:text-light transition-colors duration-200 text-sm uppercase tracking-widest font-semibold">
                <x-ri-arrow-left-long-line class="w-4 transition-transform duration-200 group-hover:-translate-x-1" />
                {{ __('Blog') }}
            </a>

            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-light/25 text-xs font-mono tracking-widest">
                @if ($blog_article->published_at)
                    <time datetime="{{ $blog_article->published_at->toDateString() }}">
                        {{ $blog_article->published_at->format('d.m.Y') }}
                    </time>
                @endif

                @php
                    $categories = $blog_article->tags->where('type', \App\Enums\TagTypes::BLOG_CATEGORY->value);
                @endphp
                @if ($categories->isNotEmpty())
                    <span>—</span>
                    <span class="uppercase">{{ $categories->pluck('name')->implode(', ') }}</span>
                @endif


                @if ($blog_article->featured)
                    <x-chip>{{ __('Featured') }}</x-chip>
                @endif
            </div>

            <h1
                class="font-black uppercase leading-none tracking-tighter text-light text-5xl lg:text-7xl xl:text-8xl max-w-5xl">
                {{ $blog_article->title }}
            </h1>

            <p class="text-light/55 text-lg lg:text-xl font-light leading-relaxed max-w-3xl">
                {{ $blog_article->summary }}
            </p>

            @php
                $tags = $blog_article->tags->where('type', \App\Enums\TagTypes::TAG->value);
            @endphp
            @if ($tags->isNotEmpty())
                <x-post-tag-list tagClasses="text-xs" :tags="$tags->mapWithKeys(fn($tag) => [$tag->name => '#'])->toArray()" />
            @endif
        </div>
    </section>

    <section class="px-8 lg:px-14 py-16 lg:py-24">
        <div
            class="prose prose-invert prose-lg max-w-3xl
                prose-headings:font-black prose-headings:uppercase prose-headings:tracking-tighter prose-headings:text-light
                prose-p:text-light/65 prose-p:font-light prose-p:leading-relaxed
                prose-a:text-light prose-a:underline prose-a:underline-offset-4 prose-a:decoration-light/30 hover:prose-a:decoration-light
                prose-strong:text-light prose-strong:font-semibold
                prose-code:text-light/80 prose-code:bg-light/5 prose-code:px-1.5 prose-code:py-0.5 prose-code:font-mono prose-code:text-sm
                prose-pre:bg-light/5 prose-pre:border prose-pre:border-light/10
                prose-blockquote:border-l-light/20 prose-blockquote:text-light/50
                prose-hr:border-light/10
                prose-img:border prose-img:border-light/10">
            {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($blog_article->content) }}
        </div>
    </section>

    @if ($blog_article->relatables->isNotEmpty())
        <section class="border-t border-light/10 px-8 lg:px-14 py-12">
            <p class="text-light/25 text-xs uppercase tracking-widest font-semibold mb-10">{{ __('Related contents') }}
            </p>
            <div class="flex flex-col gap-2">
                @foreach ($blog_article->relatables as $relatable)
                    <a wire:navigate href="{{ $relatable->show_route }}"
                        class="w-fit group flex flex-col text-light/50 hover:text-light transition-colors duration-200 text-sm">
                        <span class="text-light/25 text-[10px] tracking-widest uppercase">
                            {{ $relatable->post_type }}
                        </span>
                        <div class="flex items-center gap-3">

                            <span
                                class="underline underline-offset-4 decoration-light/20 group-hover:decoration-light transition-colors duration-200">
                                {{ $relatable->title }}
                            </span>
                            <x-ri-arrow-right-long-line class="w-3.5 shrink-0" />
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($previous || $next)
        <div
            class="grid grid-cols-1 lg:grid-cols-2 border-t border-light/10 divide-y lg:divide-y-0 lg:divide-x divide-light/10">

            @if ($previous)
                <a wire:navigate href="{{ route('blog_article.show', $previous->slug) }}"
                    class="group flex flex-col gap-4 px-8 lg:px-14 py-12 hover:bg-light/3 transition-colors duration-300">
                    <span class="text-light/25 text-xs uppercase tracking-widest font-semibold flex items-center gap-2">
                        <x-ri-arrow-left-long-line
                            class="w-3.5 transition-transform duration-200 group-hover:-translate-x-1" />
                        {{ __('Previous') }}
                    </span>
                    <span
                        class="font-black uppercase leading-none tracking-tighter text-light text-xl lg:text-2xl group-hover:text-light/70 transition-colors duration-200">
                        {{ $previous->title }}
                    </span>
                    @if ($previous->published_at)
                        <span class="text-light/25 text-xs font-mono tracking-widest">
                            {{ $previous->published_at->format('d.m.Y') }}
                        </span>
                    @endif
                </a>
            @else
                <div></div>
            @endif

            @if ($next)
                <a wire:navigate href="{{ route('blog_article.show', $next->slug) }}"
                    class="group flex flex-col gap-4 px-8 lg:px-14 py-12 hover:bg-light/3 transition-colors duration-300 lg:items-end lg:text-right">
                    <span
                        class="text-light/25 text-xs uppercase tracking-widest font-semibold flex items-center gap-2 lg:flex-row-reverse">
                        {{ __('Next') }}
                        <x-ri-arrow-right-long-line
                            class="w-3.5 transition-transform duration-200 group-hover:translate-x-1" />
                    </span>
                    <span
                        class="font-black uppercase leading-none tracking-tighter text-light text-xl lg:text-2xl group-hover:text-light/70 transition-colors duration-200">
                        {{ $next->title }}
                    </span>
                    @if ($next->published_at)
                        <span class="text-light/25 text-xs font-mono tracking-widest">
                            {{ $next->published_at->format('d.m.Y') }}
                        </span>
                    @endif
                </a>
            @endif
        </div>
    @endif

    @if ($related_blog_articles->isNotEmpty())
        <section class="border-t border-light/10 px-8 lg:px-14 py-16 lg:py-24">
            <p class="text-light/25 text-xs uppercase tracking-widest font-semibold mb-10">{{ __('Related articles') }}
            </p>
            <div class="grid grid-cols-1 lg:grid-cols-3 [&>*]:-mt-px [&>*]:-ml-px pl-px pt-px">
                @foreach ($related_blog_articles as $related_article)
                    <x-blog-article-card :article="$related_article" />
                @endforeach
            </div>
        </section>
    @endif

    <section class="bg-dark border-t border-light/10 px-8 lg:px-14 py-24 lg:py-36">
        <div class="max-w-5xl">
            <p class="font-semibold leading-tight tracking-tight text-light text-3xl lg:text-5xl 2xl:text-6xl mb-16">
                {{ __('Want to work together?') }}<br>
                <span class="text-light/25">{{ __("Let's build something great.") }}</span>
            </p>
            <a wire:navigate href="{{ route('contacts') }}"
                class="group relative overflow-hidden border border-light text-light inline-flex items-center gap-8 px-8 py-5 hover:text-dark transition-colors duration-300">
                <span
                    class="absolute inset-0 bg-light translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
                <span
                    class="relative font-semibold uppercase tracking-widest text-sm z-10">{{ __("Let's talk") }}</span>
                <x-ri-arrow-right-long-line class="z-10 w-5" />
            </a>
        </div>
    </section>
</div>
