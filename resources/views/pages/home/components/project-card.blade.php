@props(['project'])

<div
    class="border border-light p-8 2xl:p-4 flex flex-col relative hover:[&>img]:saturate-100 hover:[&>img]:brightness-100">
    <img src="{{ $project->featured_image_url }}" alt="{{ $project->title }}"
        class="absolute 2xl:static top-0 left-0 p-4 2xl:p-0 -z-1 transition-all duration-300
            2xl:saturate-100 2xl:brightness-100
            saturate-50 brightness-50
            2xl:max-h-1/2 2xl:h-1/2 2xl:min-h-1/2
            max-h-full h-f min-h-full
            object-cover object-center 2xl:mask-b-from-60%">

    <div class="gap-4 flex flex-col justify-between grow">
        <h3 class="text-2xl 2xl:text-4xl font-bold uppercase 2xl:-mt-[1em]">
            {{ $project->title }}
        </h3>
        <h4 class="text-lg 2xl:text-xl">
            {{ $project->short_description }}
        </h4>
        <div class="flex justify-between items-center [&>a]:underline [&>a]:underline-offset-4 [&>a]:text-lg">
            <a href="{{ route('project.show', $project->slug) }}">
                {{ __('Discover more') }}
            </a>


            @foreach ($project->links->pluck('url') ?? [] as $link)
                <a href="{{ $link }}" target="_blank" rel="noopener">{{ ucfirst(Uri::of($link)->host()) }}</a>
            @endforeach

            @if ($project->external_link)
                <a href="{{ $project->external_link }}" target="_blank" rel="noopener">{{ __('Website') }}</a>
            @endif
        </div>

        <x-post-tag-list :tags="$project->tags->mapWithKeys(fn($tag) => ['#' . $tag->name => '#'])->toArray()" />
    </div>
</div>
