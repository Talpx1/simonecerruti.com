@php
    $projects = collect($this->getLoadMoreData('projects'));
    $featured_projects = $projects->where('featured', true)->take(2);
    $filter_tags = $projects
        ->flatMap(fn ($project) => $project->tags->where('type', \App\Enums\TagTypes::TAG->value))
        ->groupBy('name')
        ->map->count()
        ->sortDesc();
@endphp

<div data-fx="projects-rail">
    <section class="pj-hero">
        <div>
            <h1 class="ph1" aria-label="{{ __('Work.') }}"><span class="row" data-split>{{ __('Work.') }}</span></h1>
            <p class="lead">{{ __('Real problems, solved custom and used every day. No showcase mockups: only things that work.') }}</p>
        </div>
        <div class="pj-canvas" aria-hidden="true"><canvas id="frames" data-fx="frames"></canvas></div>
    </section>

    @foreach ($featured_projects as $project)
        <x-project-feature :project="$project" />
    @endforeach

    @if ($projects->isEmpty())
        <section class="sec">
            <p class="lead mute">{{ __('No projects yet.') }}</p>
        </section>
    @else
        <section class="rail-sec" id="tutti">
            <div class="rail-head">
                <div>
                    <h2 class="h2">{!! __('All<br>the work') !!}<span class="acc">.</span></h2>
                    <div class="filters" role="group" aria-label="{{ __('Filter by type') }}">
                        <button type="button" class="on" data-f="*">{{ __('All') }}<small>{{ $projects->count() }}</small></button>
                        @foreach ($filter_tags as $tag_name => $tag_count)
                            <button type="button" data-f="{{ $tag_name }}">{{ $tag_name }}<small>{{ $tag_count }}</small></button>
                        @endforeach
                    </div>
                </div>
                <div class="rail-ctrl">
                    <button type="button" data-rail-step="-1" aria-label="{{ __('Previous') }}"
                        data-pan="cta-projects-rail-previous">←</button>
                    <button type="button" data-rail-step="1" aria-label="{{ __('Next') }}"
                        data-pan="cta-projects-rail-next">→</button>
                </div>
            </div>

            <div class="rail" id="rail" data-cursor="{{ __('Drag') }}">
                @foreach ($projects as $project)
                    <a class="rcard" wire:navigate wire:key="rail-{{ $project->id }}" href="{{ route('project.show', $project->slug) }}"
                        data-types="{{ $project->tags->where('type', \App\Enums\TagTypes::TAG->value)->pluck('name')->implode('|') }}"
                        data-cursor="{{ __('Open work') }}">
                        <x-project-cover :project="$project" />

                        <div>
                            <h3>{{ $project->title }}</h3>
                            @if ($project->kind_or_tag_names)
                                <span class="kind">{{ $project->kind_or_tag_names }}</span>
                            @endif
                        </div>

                        @if ($project->tags->where('type', \App\Enums\TagTypes::TAG->value)->isNotEmpty())
                            <div class="tags small">
                                @foreach ($project->tags->where('type', \App\Enums\TagTypes::TAG->value) as $tag)
                                    <span>{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if ($project->tags->where('type', \App\Enums\TagTypes::TECHNOLOGY->value)->isNotEmpty())
                            <div class="tags small stack">
                                @foreach ($project->tags->where('type', \App\Enums\TagTypes::TECHNOLOGY->value) as $tag)
                                    <span>{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>

            <p class="rail-empty" id="rail-empty">{{ __('No work with this tag. Yet.') }}</p>

            <div class="rail-bar"><i id="railbar"></i></div>

            @if ($this->canLoadMore('projects'))
                <div class="sec-head" style="margin-top: 40px; padding: 0 var(--gut);">
                    <button type="button" class="more" wire:click="loadMore('projects')" wire:loading.attr="disabled"
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
