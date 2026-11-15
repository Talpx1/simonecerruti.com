@props(['project', 'analyticsName'])

@php
    $url = route('project.show', $project->slug);
    $kind = $project->kind_or_tag_names;
    $topic_tags = $project->tags->where('type', \App\Enums\TagTypes::TAG->value);
    $stack_tags = $project->tags->where('type', \App\Enums\TagTypes::TECHNOLOGY->value);
@endphp

<article class="project-feature">
    <a class="project-feature-cover" href="{{ $url }}" wire:navigate data-cursor="{{ __('Open page') }}">
        <div class="project-cover">
            <div class="project-cover-inner">
                @if ($project->getFirstMedia('featured_image'))
                    <img src="{{ $project->featured_image_url }}" alt="{{ $project->title }}" loading="lazy">
                @else
                    <span class="before-after-screen-watermark" aria-hidden="true">{{ str($project->title)->substr(0, 1)->upper() }}</span>
                @endif
            </div>
        </div>
    </a>

    <div>
        <h2><a href="{{ $url }}" wire:navigate>{{ $project->title }}</a></h2>

        @if ($kind)
            <div class="project-feature-kind">{{ $kind }}</div>
        @endif

        <p class="project-feature-summary">{{ $project->short_description_or_excerpt }}</p>

        <div class="project-meta">
            @if ($project->client)
                <div><span class="eyebrow">{{ __('Client') }}</span>
                    <p>{{ $project->client->name }}</p>
                </div>
            @endif

            @if ($topic_tags->isNotEmpty())
                <div><span class="eyebrow">{{ __('Type') }}</span>
                    <div class="chip-list chip-list--small">
                        @foreach ($topic_tags as $tag)
                            <a href="{{ route('tag_archive', ['slug' => $tag->slug]) }}" wire:navigate>{{ $tag->name }}</a>
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
                    <p><a href="{{ $project->external_link }}" target="_blank" rel="noopener">{{ parse_url($project->external_link, PHP_URL_HOST) ?: $project->external_link }} ↗</a></p>
                </div>
            @endif
        </div>

        <div class="services-card-actions">
            <a class="button-primary magnetic-hover" href="{{ $url }}" wire:navigate data-pan="{{ $analyticsName }}"><span>{{ __('Open the work') }}</span><span class="button-arrow">→</span></a>
        </div>
    </div>
</article>
