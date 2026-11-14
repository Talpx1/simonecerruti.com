@props(['project'])

@php
    $type_tags = $project->tags->where('type', \App\Enums\TagTypes::TAG->value);
    $stack_tags = $project->tags->where('type', \App\Enums\TagTypes::TECHNOLOGY->value);
    $client_text = $project->client?->description ?: $project->client?->name;
@endphp

<article {{ $attributes->class('feat') }}>
    <a class="feat-cover" wire:navigate href="{{ route('project.show', $project->slug) }}"
        data-cursor="{{ __('Open work') }}" aria-label="{{ $project->title }}">
        <x-project-cover :project="$project" />
    </a>

    <div>
        <h2><a wire:navigate href="{{ route('project.show', $project->slug) }}">{{ $project->title }}</a></h2>

        @if ($project->kind_or_tag_names)
            <div class="kind">{{ $project->kind_or_tag_names }}</div>
        @endif

        <p class="sum">{{ $project->short_description }}</p>

        <div class="meta-grid">
            @if ($client_text)
                <div><span class="label">{{ __('Client') }}</span>
                    <p>{{ $client_text }}</p>
                </div>
            @endif

            @if ($type_tags->isNotEmpty())
                <div><span class="label">{{ __('Type') }}</span>
                    <div class="tags small">
                        @foreach ($type_tags as $tag)
                            <a wire:navigate href="{{ route('tag_archive', ['slug' => $tag->slug]) }}">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($stack_tags->isNotEmpty())
                <div><span class="label">{{ __('Stack') }}</span>
                    <div class="tags small stack">
                        @foreach ($stack_tags as $tag)
                            <span>{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($project->external_link)
                <div><span class="label">{{ __('Online') }}</span>
                    <p><a href="{{ $project->external_link }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Uri::of($project->external_link)->host() }} ↗</a></p>
                </div>
            @endif
        </div>

        <div class="acts">
            <a class="cta mag" wire:navigate href="{{ route('project.show', $project->slug) }}"><span>{{ __('Open work') }}</span><span
                    class="ar">→</span></a>
        </div>
    </div>
</article>
