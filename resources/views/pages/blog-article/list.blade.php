<div>
    <x-page-hero wire:ignore fx="ink" :label="__('Blog: thoughts, case studies and technical articles.')" :rows="[__('Blog.')]">
        <p>{!! __('Thoughts, case studies and technical writing on software, design and the craft of <em>building things that last.</em> Practical or technical, never filler.') !!}</p>
    </x-page-hero>

    @if ($this->featured->isNotEmpty())
        <section class="sec tight" wire:ignore>
            <div class="bfeats">
                @foreach ($this->featured as $featured_article)
                    <article @class(['bfeat rv', 'main' => $loop->first])>
                        <a class="bfeat-c" wire:navigate href="{{ route('blog_article.show', $featured_article->slug) }}"
                            data-cursor="{{ __('Read') }}" data-pan="{{ $loop->first ? 'cta-blog-featured-read' : 'card-blog-click' }}">
                            <x-blog-cover :article="$featured_article" />
                        </a>
                        <div>
                            <h2><a wire:navigate href="{{ route('blog_article.show', $featured_article->slug) }}">{{ $featured_article->title }}</a></h2>
                            <p>{{ $featured_article->summary }}</p>
                            <x-article-meta :article="$featured_article" with-author />
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="sec tight" id="articoli">
        @if ($this->featured->isNotEmpty() || $this->articles->isNotEmpty())
            <div class="sec-head">
                <h2 class="h2">{!! __('All<br>articles') !!}<span class="acc">.</span></h2>
                @include('pages.blog-article.filters')
            </div>
        @else
            @include('pages.blog-article.filters')
        @endif

        @if ($this->articles->isEmpty())
            <div class="empty">
                <div>
                    <h3>{!! __('No articles<br>yet') !!}<span class="acc">.</span></h3>
                    <p>{{ __('I write when I have something to say, not to fill a page. In the meantime, the work speaks for itself.') }}</p>
                    <div class="acts">
                        <a class="more" wire:navigate href="{{ route('projects') }}">{{ __('See the work') }} <span>→</span></a>
                        <a class="cta mag" wire:navigate href="{{ route('contacts') }}"><span>{{ __("Let's talk") }}</span><span class="ar">→</span></a>
                    </div>
                </div>
            </div>
        @else
            <div class="prows">
                @foreach ($this->articles as $article)
                    <a class="prow" wire:key="article-{{ $article->id }}" wire:navigate
                        href="{{ route('blog_article.show', $article->slug) }}" data-cursor="{{ __('Read') }}" data-pan="card-blog-click">
                        @if ($article->published_at)
                            <time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->translatedFormat('j M Y') }}</time>
                        @else
                            <span></span>
                        @endif
                        <div>
                            <h3>{{ $article->title }}</h3>
                            <p>{{ $article->summary }}</p>
                            <x-article-meta :article="$article" short />
                        </div>
                        <x-blog-cover :article="$article" class="pthumb" />
                    </a>
                @endforeach
            </div>

            {{ $this->articles->links('vendor.pagination.site') }}
        @endif
    </section>

    <x-contact-band />
</div>
