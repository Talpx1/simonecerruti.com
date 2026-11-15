@php
    $projects = collect($this->getLoadMoreData('projects'));
    $featured_projects = $projects->where('featured', true)->take(2);
    $filter_tag_types = [\App\Enums\TagTypes::TAG->value, \App\Enums\TagTypes::PROJECT_KIND->value];
    $filter_tags = $projects
        ->flatMap(fn ($project) => $project->tags->whereIn('type', $filter_tag_types))
        ->groupBy('name')
        ->map->count()
        ->sortDesc();
@endphp

<div data-effect="projects-rail">
    <section class="projects-hero">
        <div>
            <h1 class="page-hero-title" aria-label="{{ __('Work.') }}"><span class="page-hero-title-line" data-variable-letters>{{ __('Work.') }}</span></h1>
            <p class="section-lead">{{ __('Real problems, solved custom and used every day. No showcase mockups: only things that work.') }}</p>
        </div>
        <div class="projects-hero-canvas" aria-hidden="true"><canvas id="projects-3d-frames-canvas" data-effect="projects-3d-frames"></canvas></div>
    </section>

    @foreach ($featured_projects as $project)
        <x-project-feature :project="$project" />
    @endforeach

    @if ($projects->isEmpty())
        <section class="section section--tight">
            <div class="blog-empty">
                <div>
                    <h3>{!! __('No work<br>yet') !!}<span class="text-accent">.</span></h3>
                    <p>{{ __('I show a project only when it is finished and in use, not before. In the meantime, let us talk about yours.') }}</p>
                    <div class="services-card-actions">
                        <a class="button-outline-link" wire:navigate href="{{ route('how_i_work') }}">{{ __('Discover my method') }} <span>→</span></a>
                        <a class="button-primary magnetic-hover" wire:navigate href="{{ route('contacts') }}"><span>{{ __("Let's talk") }}</span><span class="button-arrow">→</span></a>
                    </div>
                </div>
            </div>
        </section>
    @else
        <section class="projects-rail-section" id="tutti">
            <div class="projects-rail-head">
                <div>
                    <h2 class="section-title">{!! __('All<br>the work') !!}<span class="text-accent">.</span></h2>
                    <div class="filter-bar" role="group" aria-label="{{ __('Filter by type') }}">
                        <button type="button" class="is-active" data-f="*">{{ __('All') }}<small>{{ $projects->count() }}</small></button>
                        @foreach ($filter_tags as $tag_name => $tag_count)
                            <button type="button" data-f="{{ $tag_name }}">{{ $tag_name }}<small>{{ $tag_count }}</small></button>
                        @endforeach
                    </div>
                </div>
                <div class="projects-rail-controls">
                    <button type="button" data-rail-step="-1" aria-label="{{ __('Previous') }}"
                        data-pan="cta-projects-rail-previous">←</button>
                    <button type="button" data-rail-step="1" aria-label="{{ __('Next') }}"
                        data-pan="cta-projects-rail-next">→</button>
                </div>
            </div>

            <div class="projects-rail" id="projects-rail" data-cursor="{{ __('Drag') }}">
                @foreach ($projects as $project)
                    <a class="projects-rail-card" wire:navigate wire:key="rail-{{ $project->id }}" href="{{ route('project.show', $project->slug) }}"
                        data-types="{{ $project->tags->whereIn('type', $filter_tag_types)->pluck('name')->unique()->implode('|') }}"
                        data-cursor="{{ __('Open work') }}">
                        <x-project-cover :project="$project" />

                        <div>
                            <h3>{{ $project->title }}</h3>
                            @if ($project->kind_names)
                                <span class="project-feature-kind">{{ $project->kind_names }}</span>
                            @endif
                        </div>

                        @if ($project->tags->where('type', \App\Enums\TagTypes::TAG->value)->isNotEmpty())
                            <div class="chip-list chip-list--small">
                                @foreach ($project->tags->where('type', \App\Enums\TagTypes::TAG->value) as $tag)
                                    <span>{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if ($project->tags->where('type', \App\Enums\TagTypes::TECHNOLOGY->value)->isNotEmpty())
                            <div class="chip-list chip-list--small chip-list--stack">
                                @foreach ($project->tags->where('type', \App\Enums\TagTypes::TECHNOLOGY->value) as $tag)
                                    <span>{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>

            <p class="projects-rail-empty" id="projects-rail-empty">{{ __('No work with this tag. Yet.') }}</p>

            <div class="projects-rail-progress"><i id="projects-rail-progress"></i></div>

            @if ($this->canLoadMore('projects'))
                <div class="section-header" style="margin-top: 40px; padding: 0 var(--site-gutter);">
                    <button type="button" class="button-outline-link" wire:click="loadMore('projects')" wire:loading.attr="disabled"
                        data-pan="cta-projects-load-more">
                        <span wire:loading.remove wire:target="loadMore('projects')">{{ __('Load more') }}</span>
                        <span wire:loading wire:target="loadMore('projects')">{{ __('Loading...') }}</span>
                    </button>
                </div>
            @endif
        </section>
    @endif

    <x-contact-band />
</div>
