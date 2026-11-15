@props(['project'])

@php
    $type_tags = $project->tags->where('type', \App\Enums\TagTypes::TAG->value);
    $stack_tags = $project->tags->where('type', \App\Enums\TagTypes::TECHNOLOGY->value);
    $client_text = $project->client?->description ?: $project->client?->name;
@endphp

<article {{ $attributes->class('project-feature') }}>
    <a class="project-feature-cover" wire:navigate href="{{ route('project.show', $project->slug) }}"
        data-cursor="{{ __('Open work') }}" aria-label="{{ $project->title }}">
        <x-project-cover :project="$project" />
    </a>

    <div>
        <h2><a wire:navigate href="{{ route('project.show', $project->slug) }}">{{ $project->title }}</a></h2>

        @if ($project->kind_or_tag_names)
            <div class="project-feature-kind">{{ $project->kind_or_tag_names }}</div>
        @endif

        <p class="project-feature-summary">{{ $project->short_description }}</p>

        <div class="project-meta">
            @if ($client_text)
                <div><span class="eyebrow">{{ __('Client') }}</span>
                    <p>{{ $client_text }}</p>
                </div>
            @endif

            @if ($type_tags->isNotEmpty())
                <div><span class="eyebrow">{{ __('Type') }}</span>
                    <div class="chip-list chip-list--small">
                        @foreach ($type_tags as $tag)
                            <a wire:navigate href="{{ route('tag_archive', ['slug' => $tag->slug]) }}">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($stack_tags->isNotEmpty())
                <div><span class="eyebrow">{{ __('Stack') }}</span>
                    <div class="chip-list chip-list--small chip-list--stack">
                        @foreach ($stack_tags as $tag)
                            <span>{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($project->external_link)
                <div><span class="eyebrow">{{ __('Online') }}</span>
                    <p><a href="{{ $project->external_link }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Uri::of($project->external_link)->host() }} ↗</a></p>
                </div>
            @endif
        </div>

        <div class="services-card-actions">
            <a class="button-primary magnetic-hover" wire:navigate href="{{ route('project.show', $project->slug) }}"><span>{{ __('Open work') }}</span><span
                    class="button-arrow">→</span></a>
        </div>
    </div>
</article>
