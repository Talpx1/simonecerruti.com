@props(['project', 'pan'])

@php
    $url = route('project.show', $project->slug);
    $kind = $project->kind_or_tag_names;
    $topic_tags = $project->tags->where('type', \App\Enums\TagTypes::TAG->value);
    $stack_tags = $project->tags->where('type', \App\Enums\TagTypes::TECHNOLOGY->value);
@endphp

<article class="feat">
    <a class="feat-cover" href="{{ $url }}" wire:navigate data-cursor="{{ __('Open page') }}">
        <div class="cover">
            <div class="in">
                @if ($project->getFirstMedia('featured_image'))
                    <img src="{{ $project->featured_image_url }}" alt="{{ $project->title }}" loading="lazy">
                @else
                    <span class="ghost" aria-hidden="true">{{ str($project->title)->substr(0, 1)->upper() }}</span>
                @endif
            </div>
        </div>
    </a>

    <div>
        <h2><a href="{{ $url }}" wire:navigate>{{ $project->title }}</a></h2>

        @if ($kind)
            <div class="kind">{{ $kind }}</div>
        @endif

        <p class="sum">{{ $project->short_description_or_excerpt }}</p>

        <div class="meta-grid">
            @if ($project->client)
                <div><span class="label">{{ __('Client') }}</span>
                    <p>{{ $project->client->name }}</p>
                </div>
            @endif

            @if ($topic_tags->isNotEmpty())
                <div><span class="label">{{ __('Type') }}</span>
                    <div class="tags small">
                        @foreach ($topic_tags as $tag)
                            <a href="{{ route('tag_archive', ['slug' => $tag->slug]) }}" wire:navigate>{{ $tag->name }}</a>
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
                    <p><a href="{{ $project->external_link }}" target="_blank" rel="noopener">{{ parse_url($project->external_link, PHP_URL_HOST) ?: $project->external_link }} ↗</a></p>
                </div>
            @endif
        </div>

        <div class="acts">
            <a class="cta mag" href="{{ $url }}" wire:navigate data-pan="{{ $pan }}"><span>{{ __('Open the work') }}</span><span class="ar">→</span></a>
        </div>
    </div>
</article>
