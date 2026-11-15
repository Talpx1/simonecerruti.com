@php
    $type_tags = $project->tags->where('type', \App\Enums\TagTypes::TAG->value);
    $stack_tags = $project->tags->where('type', \App\Enums\TagTypes::TECHNOLOGY->value);
    $title_rows = \mb_strlen($project->title) > 16 && \str_contains($project->title, ' ')
        ? [
            \Illuminate\Support\Str::beforeLast($project->title, ' '),
            \Illuminate\Support\Str::afterLast($project->title, ' '),
        ]
        : [$project->title];
    $external_host = $project->external_link ? \Illuminate\Support\Uri::of($project->external_link)->host() : null;
    $gallery = $project->getMedia('gallery');
    $has_story = filled($project->starting_point) || filled($project->goal);
    $features = collect($project->features ?? [])->filter(fn ($feature) => filled($feature['title'] ?? null));
@endphp

<div>
    <x-page-hero effect="project-blueprint" :label="$project->title" :rows="$title_rows">
        <p>{{ $project->short_description }}</p>

        <x-slot:actions>
            @if ($project->external_link)
                <a class="button-outline-link" href="{{ $project->external_link }}" target="_blank" rel="noopener"
                    data-pan="cta-project-visit-site">{{ $external_host }} <span>↗</span></a>
            @endif
            <a class="button-primary button-large magnetic-hover home-hero-cta" wire:navigate href="{{ route('contacts') }}" data-cursor="{{ __('Write to me') }}"
                data-pan="cta-project-contacts"><span>{{ __('I want something like this') }}</span><span class="button-arrow">→</span></a>
        </x-slot:actions>
    </x-page-hero>

    <section class="section section--tight">
        <div class="project-detail-top">
            <div class="before-after-screen">
                <div class="before-after-screen-after">
                    @if ($project->hasMedia('featured_image'))
                        <img src="{{ $project->featured_image_url }}" alt="{{ $project->title }}">
                    @else
                        <span class="before-after-screen-watermark" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($project->title, 0, 1)) }}</span>
                    @endif
                </div>
            </div>

            <div class="project-detail-facts">
                @if ($project->client)
                    <div><span class="eyebrow">{{ __('Client') }}</span>
                        <p>
                            @if ($project->client->description)
                                {{ $project->client->description }}
                            @else
                                {{ $project->client->name }}
                            @endif
                        </p>
                    </div>
                @endif

                @if ($type_tags->isNotEmpty())
                    <div><span class="eyebrow">{{ __('Type') }}</span>
                        <div class="chip-list taglinks">
                            @foreach ($type_tags as $tag)
                                <a wire:navigate href="{{ route('tag_archive', ['slug' => $tag->slug]) }}">{{ $tag->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($stack_tags->isNotEmpty())
                    <div><span class="eyebrow">{{ __('Stack') }}</span>
                        <div class="chip-list">
                            @foreach ($stack_tags as $tag)
                                <span>{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($project->links->isNotEmpty())
                    <div><span class="eyebrow">{{ __('Links') }}</span>
                        @foreach ($project->links->pluck('url') as $link)
                            <p><a href="{{ $link }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Uri::of($link)->host() }} ↗</a></p>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($has_story)
        <section class="section project-detail-story">
            <div class="section-header"><h2 class="section-title reveal">{{ __('The work') }}<span class="text-accent">.</span></h2></div>
            <div class="project-detail-prose reveal">
                @if (filled($project->starting_point))
                    <p><b>{{ __('The starting point.') }}</b> {{ $project->starting_point }}</p>
                @endif
                @if (filled($project->goal))
                    <p><b>{{ __('The goal.') }}</b> {{ $project->goal }}</p>
                @endif
            </div>
        </section>
    @endif

    @if (filled($project->outcome))
        <section class="section scroll-lit"><p>{{ $project->outcome }}</p></section>
    @endif

    @if ($features->isNotEmpty())
        <section class="section">
            <div class="section-header">
                <h2 class="section-title reveal">{!! __('Features<br>and challenges') !!}<span class="text-accent">.</span></h2>
                <p class="section-lead section-lead--muted reveal">{{ __('Hover over. Or tap, on a phone.') }}</p>
            </div>
            <div class="accordion">
                @foreach ($features as $feature)
                    <button class="accordion-item" type="button" x-data x-on:click="$el.classList.toggle('open')">
                        <div class="accordion-item-head"><span>{{ $feature['title'] }}</span><em>+</em></div>
                        <div class="accordion-item-body">
                            <div>
                                <p>{{ $feature['description'] ?? '' }}</p>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        </section>
    @endif

    @if (! $has_story && ! $features->count() && filled($project->description))
        <section class="section project-detail-story">
            <div class="section-header"><h2 class="section-title reveal">{{ __('The work') }}<span class="text-accent">.</span></h2></div>
            <div class="project-detail-prose reveal">
                {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($project->description) }}
            </div>
        </section>
    @endif

    @if ($gallery->isNotEmpty())
        <section class="section">
            <div class="section-header"><h2 class="section-title reveal">{{ __('Gallery') }}<span class="text-accent">.</span></h2></div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($gallery as $media)
                    <picture>
                        @if ($media->hasGeneratedConversion('gallery_webp'))
                            <source srcset="{{ $media->getUrl('gallery_webp') }}" type="image/webp">
                        @endif
                        <img src="{{ $media->getUrl() }}" alt="{{ $media->name }}" loading="lazy" class="w-full object-cover">
                    </picture>
                @endforeach
            </div>
        </section>
    @endif

    @if (! $adjacent_projects['previous']->is($project))
        <nav class="previous-next-navigation" aria-label="{{ __('Other work') }}">
            <a class="previous-next-navigation-previous" wire:navigate href="{{ route('project.show', $adjacent_projects['previous']->slug) }}"
                data-cursor="{{ __('Back') }}" data-pan="cta-project-previous"><span class="eyebrow">← {{ __('Previous work') }}</span>
                <h3>{{ $adjacent_projects['previous']->title }}</h3></a>
            <a class="previous-next-navigation-next" wire:navigate href="{{ route('project.show', $adjacent_projects['next']->slug) }}"
                data-cursor="{{ __('Forward') }}" data-pan="cta-project-next"><span class="eyebrow">{{ __('Next work') }} →</span>
                <h3>{{ $adjacent_projects['next']->title }}</h3></a>
        </nav>
    @endif

    <x-contact-band />
</div>
