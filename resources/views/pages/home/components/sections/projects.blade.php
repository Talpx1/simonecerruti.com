<section
    class="p-4 2xl:p-16 grid grid-cols-1 lg:grid-cols-3 lg:grid-rows-2 gap-4 2xl:gap-16 min-h-screen lg:h-screen lg:max-h-screen">
    <div class="space-y-4 py-16">
        <h2 class="text-5xl lg:text-6xl 2xl:text-8xl font-black uppercase">{{ __('Projects') }}</h2>
        <p class="text-xl lg:text-2xl 2xl:text-4xl font-semibold uppercase">
            {{ __('Discover what I\'m working on') }}
        </p>
        <a wire:navigate href="{{ route('projects') }}"
            class="text-xl lg:text-2xl underline underline-offset-4 flex items-center gap-1">
            {{ __('See all') }} <x-ri-arrow-right-long-line class="w-6" />
        </a>
        <x-post-tag-list :tags="$project_tags->mapWithKeys(fn($tag) => ['#' . $tag->name => '#'])->toArray()" />
    </div>
    @foreach ($projects as $project)
        <x-pages::home.components.project-card :$project />
    @endforeach
</section>
