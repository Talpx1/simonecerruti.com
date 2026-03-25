<div>
    <section class="flex flex-col px-8 lg:px-14 pb-20 pt-16">
        <div class="flex items-end justify-between gap-8">
            <h1 class="font-black uppercase leading-none tracking-tight text-6xl lg:text-8xl xl:text-9xl">
                {{ __('Projects') }}
            </h1>

            @if ($this->isLoadMoreNotEmpty('projects'))
                <span class="text-light/25 font-mono text-sm tracking-widest mb-2 shrink-0">
                    {{ str_pad((string) $this->getLoadMoreTotal('projects'), 2, '0', STR_PAD_LEFT) }}
                </span>
            @endif
        </div>

        <p class="mt-10 text-light/60 text-lg lg:text-xl font-light leading-relaxed max-w-2xl">
            {{ __('A selection of work built with care. Designed to last, scale, and solve a real problem.') }}
        </p>
    </section>

    <section class="border-t border-light/10">
        @if ($this->isLoadMoreEmpty('projects'))
            <div class="px-8 lg:px-14 py-36 text-light/25 text-sm uppercase tracking-widest font-semibold">
                {{ __('No projects yet.') }}
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 ">
                @foreach ($this->getLoadMoreData('projects') as $project)
                    <x-project-card :project="$project" :index="$loop->index" />
                @endforeach
            </div>

            @if ($this->canLoadMore('projects'))
                <div class="border-t border-light/10 px-8 lg:px-14 py-12 flex items-center justify-between">
                    <span class="text-light/25 text-sm font-mono tracking-widest">
                        {{ count($this->getLoadMoreData('projects')) }} / {{ $this->getLoadMoreTotal('projects') }}
                    </span>

                    <button wire:click="loadMore('projects')" wire:loading.attr="disabled"
                        class="group relative overflow-hidden border border-light/20 text-light/50 flex items-center gap-4 px-8 py-4 hover:text-dark hover:border-light transition-colors duration-300">
                        <span
                            class="absolute inset-0 bg-light translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
                        <span class="relative z-10 font-semibold uppercase tracking-widest text-sm">
                            <span wire:loading.remove wire:target="loadMore('projects')">{{ __('Load more') }}</span>
                            <span wire:loading wire:target="loadMore('projects')">{{ __('Loading...') }}</span>
                        </span>
                        <x-ri-arrow-down-long-line class="relative z-10 w-4 wire:loading:hidden" wire:loading.remove
                            wire:target="loadMore('projects')" />
                    </button>
                </div>
            @endif
        @endif
    </section>

    <section class="bg-dark border-t border-light/10 px-8 lg:px-14 py-24 lg:py-36">
        <div class="max-w-5xl">
            <p class="font-semibold leading-tight tracking-tight text-light text-3xl lg:text-5xl 2xl:text-6xl mb-16">
                {{ __('Have a project in mind?') }}<br>
                <span class="text-light/25">{{ __("Let's make it happen.") }}</span>
            </p>

            <a wire:navigate href="{{ route('contacts') }}"
                class="group relative overflow-hidden border border-light text-light inline-flex items-center justify-between gap-8 px-8 py-5 hover:text-dark transition-colors duration-300">
                <span
                    class="absolute inset-0 bg-light translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
                <span
                    class="relative font-semibold uppercase tracking-widest text-sm z-10">{{ __("Let's talk") }}</span>
                <x-ri-arrow-right-long-line class="z-10 w-5" />
            </a>
        </div>
    </section>
</div>
