@props(['project', 'index' => null, 'eager' => false])

<article class="group relative flex flex-col overflow-hidden border border-light/10">

    @if ($project->featured_image_url)
        <div class="overflow-hidden h-56 lg:h-64 border-b border-light/10 relative">
            <picture>
                @if ($project->getFirstMedia('featured_image')?->hasGeneratedConversion('featured_image_webp'))
                    <source srcset="{{ $project->getFirstMediaUrl('featured_image', 'featured_image_webp') }}"
                        type="image/webp">
                @endif
                <img src="{{ $project->featured_image_url }}" alt="{{ $project->title }}"
                    loading="{{ $eager || ($index !== null && $index < 3) ? 'eager' : 'lazy' }}"
                    class="w-full h-full object-cover object-center saturate-50 brightness-75 group-hover:saturate-100 group-hover:brightness-90 group-hover:scale-105 transition-all duration-500">
            </picture>
        </div>
    @endif

    <div class="flex flex-col justify-between gap-6 p-8 grow">
        <div class="space-y-3">
            <h2 class="font-black uppercase leading-none tracking-tighter text-light text-2xl lg:text-3xl">
                {{ $project->title }}
            </h2>
            <p class="text-light/50 text-sm leading-relaxed font-light">
                {{ $project->short_description }}
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <a wire:navigate href="{{ route('project.show', $project->slug) }}"
                class="group/link relative overflow-hidden border border-light/20 text-light/50 flex items-center gap-3 px-5 py-3 hover:text-dark hover:border-light transition-colors duration-300">
                <span
                    class="absolute inset-0 bg-light translate-y-full group-hover/link:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
                <span class="relative z-10 text-xs font-semibold uppercase tracking-widest">{{ __('View') }}</span>
                <x-ri-arrow-right-long-line class="relative z-10 w-3.5" />
            </a>

            @if ($project->external_link)
                <a href="{{ $project->external_link }}" target="_blank" rel="noopener"
                    class="group/ext relative overflow-hidden border border-light/10 text-light/30 flex items-center gap-3 px-5 py-3 hover:text-dark hover:border-light transition-colors duration-300">
                    <span
                        class="absolute inset-0 bg-light translate-y-full group-hover/ext:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
                    <span
                        class="relative z-10 text-xs font-semibold uppercase tracking-widest">{{ __('Website') }}</span>
                    <x-ri-external-link-line class="relative z-10 w-3.5" />
                </a>
            @endif
        </div>

        <x-post-tag-list :tags="$project->tags->mapWithKeys(fn($tag) => ['#' . $tag->name => '#'])->toArray()" />
    </div>
</article>
