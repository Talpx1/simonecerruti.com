<div>
    {{-- Hero --}}
    <section class="flex flex-col px-8 lg:px-14 pt-16 pb-20 border-b border-light/10">
        <div class="flex items-end justify-between gap-8">
            <h1 class="font-black uppercase leading-none tracking-tight text-7xl lg:text-8xl xl:text-9xl">
                <span class="text-light/20">#</span>{{ $tag->name }}
            </h1>

            @php
                $total = $articles_total + $projects_total;
            @endphp
            @if ($total > 0)
                <span class="text-light/25 font-mono text-sm tracking-widest mb-2 shrink-0">
                    {{ str_pad((string) $total, 2, '0', STR_PAD_LEFT) }}
                </span>
            @endif
        </div>

        <p class="mt-10 text-light/60 text-lg lg:text-xl font-light leading-relaxed max-w-2xl">
            {!! __('All content tagged with :name — articles, projects and more.', [
                'name' => '<span class="text-light">#' . $tag->name . '</span>',
            ]) !!}
        </p>
    </section>

    {{-- Empty state --}}
    @if ($articles->isEmpty() && $projects->isEmpty())
        <section class="px-8 lg:px-14 py-36 text-light/25 text-sm uppercase tracking-widest font-semibold">
            {{ __('No content found for this tag.') }}
        </section>
    @endif

    {{-- Articles section --}}
    @if ($articles->isNotEmpty())
        <section class="border-b border-light/10" x-data="carousel()">
            {{-- Section header --}}
            <div class="flex items-center justify-between px-8 lg:px-14 py-8 border-b border-light/10">
                <div class="flex items-center gap-6">
                    <p class="text-light/25 text-xs uppercase tracking-widest font-semibold">
                        {{ __('Articles') }}
                    </p>
                    <span class="text-light/20 font-mono text-xs tracking-widest">
                        {{ str_pad((string) $articles_total, 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                {{-- Carousel controls --}}
                @if ($articles->count() > 1)
                    <div class="flex gap-2">
                        <button @click="prev()" :disabled="isFirst"
                            class="border border-light/20 p-2 text-light/40 hover:text-light hover:border-light disabled:opacity-20 disabled:cursor-not-allowed transition-colors duration-200"
                            aria-label="{{ __('Previous') }}">
                            <x-ri-arrow-left-long-line class="w-4" />
                        </button>
                        <button @click="next()" :disabled="isLast"
                            class="border border-light/20 p-2 text-light/40 hover:text-light hover:border-light disabled:opacity-20 disabled:cursor-not-allowed transition-colors duration-200"
                            aria-label="{{ __('Next') }}">
                            <x-ri-arrow-right-long-line class="w-4" />
                        </button>
                    </div>
                @endif
            </div>

            {{-- Carousel track --}}
            <div x-ref="track" @scroll.passive="onScroll()"
                class="flex overflow-x-auto snap-x snap-mandatory scrollbar-none scroll-smooth [&>*]:snap-start [&>*]:shrink-0">
                @foreach ($articles as $article)
                    <div class="w-[85vw] sm:w-[60vw] lg:w-[38vw] xl:w-[30vw] border-r border-light/10 last:border-r-0">
                        <x-blog-article-card :article="$article" :index="$loop->index" />
                    </div>
                @endforeach
            </div>

            {{-- Progress dots --}}
            @if ($articles->count() > 1)
                <div class="flex gap-1.5 px-8 lg:px-14 py-6 border-t border-light/10">
                    @foreach ($articles as $article)
                        <div class="h-px transition-all duration-300 bg-light"
                            :class="currentIndex === {{ $loop->index }} ?
                                'w-8 opacity-60' :
                                'w-3 opacity-20'">
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    {{-- Projects section --}}
    @if ($projects->isNotEmpty())
        <section class="border-b border-light/10" x-data="carousel()">
            {{-- Section header --}}
            <div class="flex items-center justify-between px-8 lg:px-14 py-8 border-b border-light/10">
                <div class="flex items-center gap-6">
                    <p class="text-light/25 text-xs uppercase tracking-widest font-semibold">
                        {{ __('Projects') }}
                    </p>
                    <span class="text-light/20 font-mono text-xs tracking-widest">
                        {{ str_pad((string) $projects_total, 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                @if ($projects->count() > 1)
                    <div class="flex gap-2">
                        <button @click="prev()" :disabled="isFirst"
                            class="border border-light/20 p-2 text-light/40 hover:text-light hover:border-light disabled:opacity-20 disabled:cursor-not-allowed transition-colors duration-200"
                            aria-label="{{ __('Previous') }}">
                            <x-ri-arrow-left-long-line class="w-4" />
                        </button>
                        <button @click="next()" :disabled="isLast"
                            class="border border-light/20 p-2 text-light/40 hover:text-light hover:border-light disabled:opacity-20 disabled:cursor-not-allowed transition-colors duration-200"
                            aria-label="{{ __('Next') }}">
                            <x-ri-arrow-right-long-line class="w-4" />
                        </button>
                    </div>
                @endif
            </div>

            {{-- Carousel track --}}
            <div x-ref="track" @scroll.passive="onScroll()"
                class="flex overflow-x-auto snap-x snap-mandatory scrollbar-none scroll-smooth [&>*]:snap-start [&>*]:shrink-0">
                @foreach ($projects as $project)
                    <div class="w-[85vw] sm:w-[60vw] lg:w-[38vw] xl:w-[30vw] border-r border-light/10 last:border-r-0">
                        <x-project-card :project="$project" :index="$loop->index" />
                    </div>
                @endforeach
            </div>

            @if ($projects->count() > 1)
                <div class="flex gap-1.5 px-8 lg:px-14 py-6 border-t border-light/10">
                    @foreach ($projects as $project)
                        <div class="h-px transition-all duration-300 bg-light"
                            :class="currentIndex === {{ $loop->index }} ?
                                'w-8 opacity-60' :
                                'w-3 opacity-20'">
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @endif
</div>

<script>
    function carousel() {
        return {
            currentIndex: 0,

            get isFirst() {
                return this.currentIndex === 0;
            },

            get isLast() {
                const track = this.$refs.track;
                if (!track) return true;
                return this.currentIndex >= track.children.length - 1;
            },

            next() {
                const track = this.$refs.track;
                if (!track) return;
                const item_width = track.children[0]?.offsetWidth ?? 0;
                track.scrollBy({
                    left: item_width,
                    behavior: 'smooth'
                });
            },

            prev() {
                const track = this.$refs.track;
                if (!track) return;
                const item_width = track.children[0]?.offsetWidth ?? 0;
                track.scrollBy({
                    left: -item_width,
                    behavior: 'smooth'
                });
            },

            onScroll() {
                const track = this.$refs.track;
                if (!track) return;
                const item_width = track.children[0]?.offsetWidth ?? 1;
                this.currentIndex = Math.round(track.scrollLeft / item_width);
            },
        };
    }
</script>
