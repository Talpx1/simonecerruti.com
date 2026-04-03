<div>
    <section class="grid grid-cols-1 lg:grid-cols-[1fr_auto] min-h-[80vh] border-b border-light/10">

        <div class="flex flex-col justify-between px-8 lg:px-14 pt-16 pb-12 lg:border-r border-light/10">

            <div class="flex items-center justify-between mb-12 lg:mb-20">
                <a wire:navigate href="{{ route('projects') }}"
                    class="group flex items-center gap-3 text-light/40 hover:text-light transition-colors duration-200 text-sm uppercase tracking-widest font-semibold">
                    <x-ri-arrow-left-long-line
                        class="w-4 transition-transform duration-200 group-hover:-translate-x-1" />
                    {{ __('Projects') }}
                </a>
            </div>

            <div class="space-y-6">
                <h1
                    class="font-black uppercase leading-none tracking-tighter text-5xl lg:text-8xl xl:text-9xl text-light">
                    {{ $project->title }}
                </h1>

                <p class="text-light/55 text-lg lg:text-xl font-light leading-relaxed max-w-2xl">
                    {{ $project->short_description }}
                </p>
            </div>

            <div class="mt-12 flex flex-col sm:flex-row sm:items-end justify-between gap-6">
                <x-post-tag-list :tags="$project->tags
                    ->where('type', \App\Enums\TagTypes::TAG->value)
                    ->mapWithKeys(fn($tag) => [$tag->name => '#'])
                    ->toArray()" />

                <div class="flex gap-4">
                    @if ($project->external_link)
                        <a href="{{ $project->external_link }}" target="_blank" rel="noopener"
                            class="group relative overflow-hidden border border-light text-light flex items-center gap-3 px-6 py-3 hover:text-dark transition-colors duration-300 text-sm font-semibold uppercase tracking-widest">
                            <span
                                class="absolute inset-0 bg-light translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
                            <span class="relative z-10">{{ __('Visit site') }}</span>
                            <x-ri-arrow-right-long-line class="relative z-10 w-4" />
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @if ($project->featured_image_url)
            <div class="hidden lg:block w-[38vw] max-w-2xl relative overflow-hidden">
                <img src="{{ $project->featured_image_url }}" alt="{{ $project->title }}"
                    class="absolute inset-0 w-full h-full object-cover object-center saturate-50 brightness-75 mask-b-from-80%">
            </div>
        @endif
    </section>

    @if ($project->featured_image_url)
        <div class="lg:hidden h-64 overflow-hidden border-b border-light/10 relative">
            <img src="{{ $project->featured_image_url }}" alt="{{ $project->title }}"
                class="w-full h-full object-cover object-center saturate-50 brightness-75">
        </div>
    @endif

    <section class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] divide-y lg:divide-y-0 lg:divide-x divide-light/10">
        <div
            class="px-8 lg:px-14 py-20 lg:py-28 space-y-6 text-light/70 text-base lg:text-lg font-light leading-relaxed">
            {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($project->description) }}
        </div>

        <aside class="px-8 lg:px-12 py-20 lg:py-28 flex flex-col gap-12">

            @if ($project->client)
                <div class="space-y-2">
                    <p class="text-light/25 text-xs uppercase tracking-widest font-semibold">{{ __('Client') }}</p>
                    <p class="text-light font-semibold text-lg">{{ $project->client }}</p>
                </div>
            @endif

            @if ($project->tags->isNotEmpty())
                <div class="space-y-3">
                    <p class="text-light/25 text-xs uppercase tracking-widest font-semibold">{{ __('Stack') }}</p>
                    <x-chip-list size="xs" :entries="$project->tags
                        ->where('type', \App\Enums\TagTypes::TECHNOLOGY->value)
                        ->pluck('name')
                        ->toArray()" />
                </div>
            @endif

            @if ($project->links->isNotEmpty() || $project->external_link)
                <div class="space-y-3">
                    <p class="text-light/25 text-xs uppercase tracking-widest font-semibold">{{ __('Links') }}</p>
                    <div class="flex flex-col gap-2">
                        @if ($project->links->isNotEmpty())
                            @foreach ($project->links->pluck('url') as $link)
                                <a href="{{ $link }}" target="_blank" rel="noopener"
                                    class="flex items-center gap-2 text-light/50 hover:text-light transition-colors duration-200 text-sm underline underline-offset-4">
                                    {{ ucfirst(Uri::of($link)->host()) }}
                                    <x-ri-external-link-line class="w-3" />
                                </a>
                            @endforeach
                        @endif

                        @if ($project->external_link)
                            <a href="{{ $project->external_link }}" target="_blank" rel="noopener"
                                class="flex items-center gap-2 text-light/50 hover:text-light transition-colors duration-200 text-sm underline underline-offset-4">
                                {{ __('Website') }}
                                <x-ri-external-link-line class="w-3" />
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </aside>
    </section>

    @if ($project->getMedia('gallery')->isNotEmpty())
        <section class="border-t border-light/10 px-8 lg:px-14 py-20 lg:py-28">
            <p class="text-light/25 text-xs uppercase tracking-widest font-semibold mb-10">{{ __('Gallery') }}</p>

            <div class="columns-1 sm:columns-2 lg:columns-3 gap-4 space-y-4">
                @foreach ($project->getMedia('gallery') as $media)
                    <picture>
                        @if ($media->hasGeneratedConversion('gallery_webp'))
                            <source srcset="{{ $media->getUrl('gallery_webp') }}" type="image/webp">
                        @endif
                        <img src="{{ $media->getUrl() }}" alt="{{ $media->name }}" loading="lazy"
                            class="w-full object-cover saturate-75 hover:saturate-100 transition-all duration-500 break-inside-avoid">
                    </picture>
                @endforeach
            </div>
        </section>
    @endif

    <section class="bg-dark border-t border-light/10 px-8 lg:px-14 py-24 lg:py-36">
        <div class="max-w-5xl">
            <p class="font-semibold leading-tight tracking-tight text-light text-3xl lg:text-5xl 2xl:text-6xl mb-16">
                {{ __('Liked what you saw?') }}<br>
                <span class="text-light/25">{{ __("Let's build something together.") }}</span>
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a wire:navigate href="{{ route('contacts') }}"
                    class="group relative overflow-hidden border border-light text-light flex items-center justify-between gap-8 px-8 py-5 hover:text-dark transition-colors duration-300">
                    <span
                        class="absolute inset-0 bg-light translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
                    <span
                        class="relative font-semibold uppercase tracking-widest text-sm z-10">{{ __("Let's talk") }}</span>
                    <x-ri-arrow-right-long-line class="z-10 w-5" />
                </a>

                <a wire:navigate href="{{ route('projects') }}"
                    class="group relative overflow-hidden border border-light/20 text-light/50 flex items-center justify-between gap-8 px-8 py-5 hover:text-dark hover:border-light transition-colors duration-300">
                    <span
                        class="absolute inset-0 bg-light translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
                    <span
                        class="relative font-semibold uppercase tracking-widest text-sm z-10">{{ __('All projects') }}</span>
                    <x-ri-arrow-right-long-line class="z-10 w-5" />
                </a>
            </div>
        </div>
    </section>
</div>
