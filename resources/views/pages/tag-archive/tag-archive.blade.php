@php
    $service_route = $tag->service?->getRouteName();
@endphp

<div data-fx="projects-rail">
    <x-page-hero fx="bp" :label="__('Work: :tag', ['tag' => $tag->name])" :rows="[$tag->name.'.']">
        @if (filled($tag->description))
            <p>{{ $tag->description }}</p>
        @endif

        <x-slot:actions>
            @if ($service_route)
                <a class="more" wire:navigate href="{{ route($service_route) }}" data-pan="cta-tag-service">{{ __('The service') }}
                    <span>→</span></a>
            @endif
            <a class="more" wire:navigate href="{{ route('projects') }}" data-pan="cta-tag-projects">{{ __('All work') }}
                <span>→</span></a>
            <a class="cta xl mag hero-cta" wire:navigate href="{{ route('contacts') }}" data-cursor="{{ __('Write to me') }}"
                data-pan="cta-tag-contacts"><span>{{ __("Let's talk") }}</span><span class="ar">→</span></a>
        </x-slot:actions>
    </x-page-hero>

    @foreach ($projects as $project)
        <x-project-feature :project="$project" />
    @endforeach

    @if ($articles->isNotEmpty())
        <section class="sec">
            <div class="sec-head"><h2 class="h2 rv">{{ __('Articles') }}<span class="acc">.</span></h2></div>
            <div class="prows">
                @foreach ($articles as $article)
                    <a class="prow" wire:navigate href="{{ route('blog_article.show', $article->slug) }}" data-cursor="{{ __('Read') }}">
                        <time datetime="{{ $article->published_at?->toDateString() }}">{{ $article->published_at?->translatedFormat('d M Y') }}</time>
                        <div>
                            <h3>{{ $article->title }}</h3>
                            <p>{{ $article->summary_or_excerpt }}</p>
                        </div>
                        <span class="pthumb" aria-hidden="true"><i>{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($article->title, 0, 1)) }}</i></span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($articles->isEmpty() && $projects->isEmpty())
        <section class="sec">
            <p class="lead mute">{{ __('No content found for this tag.') }}</p>
        </section>
    @endif

    @if ($all_tags->isNotEmpty())
        <section class="sec">
            <div class="sec-head"><h2 class="h2 rv">{!! __('Search<br>by type') !!}<span class="acc">.</span></h2></div>
            <nav class="tagcloud" aria-label="{{ __('All types of work') }}">
                @foreach ($all_tags as $other_tag)
                    <a wire:navigate href="{{ route('tag_archive', ['slug' => $other_tag->slug]) }}"
                        @class(['here' => $other_tag->is($tag)]) @if ($other_tag->is($tag)) aria-current="page" @endif>{{ $other_tag->name }}</a>
                @endforeach
            </nav>
        </section>
    @endif

    <x-contact-band />
</div>
