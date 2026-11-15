@php
    $service_route = $tag->service?->getRouteName();
@endphp

<div data-effect="projects-rail">
    <x-page-hero effect="project-blueprint" :label="__('Work: :tag', ['tag' => $tag->name])" :rows="[$tag->name.'.']">
        @if (filled($tag->description))
            <p>{{ $tag->description }}</p>
        @endif

        <x-slot:actions>
            @if ($service_route)
                <a class="button-outline-link" wire:navigate href="{{ route($service_route) }}" data-pan="cta-tag-service">{{ __('The service') }}
                    <span>→</span></a>
            @endif
            <a class="button-outline-link" wire:navigate href="{{ route('projects') }}" data-pan="cta-tag-projects">{{ __('All work') }}
                <span>→</span></a>
            <a class="button-primary button-large magnetic-hover home-hero-cta" wire:navigate href="{{ route('contacts') }}" data-cursor="{{ __('Write to me') }}"
                data-pan="cta-tag-contacts"><span>{{ __("Let's talk") }}</span><span class="button-arrow">→</span></a>
        </x-slot:actions>
    </x-page-hero>

    @foreach ($projects as $project)
        <x-project-feature :project="$project" />
    @endforeach

    @if ($articles->isNotEmpty())
        <section class="section">
            <div class="section-header"><h2 class="section-title reveal">{{ __('Articles') }}<span class="text-accent">.</span></h2></div>
            <div class="blog-list">
                @foreach ($articles as $article)
                    <a class="blog-list-item" wire:navigate href="{{ route('blog_article.show', $article->slug) }}" data-cursor="{{ __('Read') }}">
                        <time datetime="{{ $article->published_at?->toDateString() }}">{{ $article->published_at?->translatedFormat('d M Y') }}</time>
                        <div>
                            <h3>{{ $article->title }}</h3>
                            <p>{{ $article->summary_or_excerpt }}</p>
                        </div>
                        <span class="post-thumbnail" aria-hidden="true"><i>{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($article->title, 0, 1)) }}</i></span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($articles->isEmpty() && $projects->isEmpty())
        <section class="section">
            <p class="section-lead section-lead--muted">{{ __('No content found for this tag.') }}</p>
        </section>
    @endif

    @if ($all_tags->isNotEmpty())
        <section class="section">
            <div class="section-header"><h2 class="section-title reveal">{!! __('Search<br>by type') !!}<span class="text-accent">.</span></h2></div>
            <nav class="tag-cloud" aria-label="{{ __('All types of work') }}">
                @foreach ($all_tags as $other_tag)
                    <a wire:navigate href="{{ route('tag_archive', ['slug' => $other_tag->slug]) }}"
                        @class(['is-current' => $other_tag->is($tag)]) @if ($other_tag->is($tag)) aria-current="page" @endif>{{ $other_tag->name }}</a>
                @endforeach
            </nav>
        </section>
    @endif

    <x-contact-band />
</div>
