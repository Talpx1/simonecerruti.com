@php
    $share_url = route('blog_article.show', $blog_article->slug);
    $categories = $blog_article->tags->where('type', \App\Enums\TagTypes::BLOG_CATEGORY->value);
    $tags = $blog_article->tags->where('type', \App\Enums\TagTypes::TAG->value);
    $lightbox_labels = [
        'close' => __('Close'),
        'previous' => __('Previous image'),
        'next' => __('Next image'),
        'gallery' => __('Gallery'),
        'open' => __('Open image'),
    ];
@endphp

<div>
    <section class="page-hero art-hero" wire:ignore>
        <canvas id="field" class="hero-bg" data-fx="field" aria-hidden="true"></canvas>
        <h1 class="art-h1">{{ $blog_article->title }}</h1>
        <div class="ph-sub"><p>{{ $blog_article->summary }}</p></div>
        <x-article-meta :article="$blog_article" with-author />
    </section>

    @if ($blog_article->hasMedia('featured_image'))
        <figure class="art-cover rv" wire:ignore>
            <img src="{{ $blog_article->featured_image_url }}" alt="{{ $blog_article->title }}" loading="eager">
        </figure>
    @endif

    <div class="art-grid" data-fx="article-reader" wire:ignore.self>
        <aside class="art-side" aria-label="{{ __('In this article') }}">
            <div class="art-prog" aria-hidden="true"><i></i></div>

            @if (count($table_of_contents) > 0)
                <nav class="toc" aria-label="{{ __('Contents') }}">
                    @foreach ($table_of_contents as $entry)
                        <a @class(['l3' => $entry['level'] === 3]) href="#{{ $entry['id'] }}">{{ $entry['title'] }}</a>
                    @endforeach
                </nav>
            @else
                <div></div>
            @endif

            <div class="share">
                <span class="label">{{ __('Share') }}</span>
                <button type="button" class="ico" data-copy-link="{{ $share_url }}" aria-label="{{ __('Copy link') }}" data-cursor="{{ __('Copy') }}" data-pan="cta-blog-article-share-copy"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/></svg></button>
                <a class="ico" href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($share_url) }}" target="_blank" rel="noopener" aria-label="{{ __('Share on LinkedIn') }}" data-pan="cta-blog-article-share-linkedin"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18"/><path d="M8 10v7M8 7v.01M12 17v-7M12 13c0-2 1.5-3 3-3s2 1 2 3v4"/></svg></a>
                <a class="ico" href="https://x.com/intent/post?url={{ urlencode($share_url) }}&text={{ rawurlencode($blog_article->title) }}" target="_blank" rel="noopener" aria-label="{{ __('Share on X') }}" data-pan="cta-blog-article-share-x"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4l16 16M20 4L4 20" transform="scale(.82) translate(2.6 2.6)"/></svg></a>
                <a class="ico" href="https://wa.me/?text={{ rawurlencode($blog_article->title.' '.$share_url) }}" target="_blank" rel="noopener" aria-label="{{ __('Share on WhatsApp') }}" data-pan="cta-blog-article-share-whatsapp"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20l1.2-4A8 8 0 1 1 8 18.8z"/></svg></a>
            </div>
        </aside>

        <article class="art-body" data-fx="lightbox" data-labels="{{ json_encode($lightbox_labels, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT) }}">{!! $content_html !!}</article>
    </div>

    <section class="sec tight">
        @if ($author_bio)
            <div class="author rv">
                <span class="au-ph" aria-hidden="true">{{ $blog_article->author->initials }}</span>
                <div>
                    <h3>{{ $blog_article->author->name }}</h3>
                    <p>{{ $author_bio }}</p>
                    <a class="more" wire:navigate href="{{ route('about') }}" data-pan="cta-blog-article-about">{{ __('About me') }} <span>→</span></a>
                </div>
            </div>
        @endif

        @if ($categories->isNotEmpty() || $tags->isNotEmpty())
            <div class="art-tags">
                @foreach ($categories as $category)
                    <a wire:navigate href="{{ route('blog', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                @endforeach
                @foreach ($tags as $tag)
                    <a wire:navigate href="{{ route('tag_archive', ['slug' => $tag->slug]) }}">{{ $tag->name }}</a>
                @endforeach
            </div>
        @endif

        @if ($blog_article->relatables->isNotEmpty())
            <p class="label" style="margin-top: 36px">{{ __('Related contents') }}</p>
            <div class="art-tags">
                @foreach ($blog_article->relatables as $relatable)
                    <a wire:navigate href="{{ $relatable->show_route }}" data-pan="cta-blog-article-related">{{ $relatable->title }}</a>
                @endforeach
            </div>
        @endif
    </section>

    @if ($previous || $next)
        <nav class="pn" aria-label="{{ __('More articles') }}">
            @if ($previous)
                <a class="pn-prev" wire:navigate href="{{ route('blog_article.show', $previous->slug) }}" data-cursor="{{ __('Back') }}">
                    <span class="label">← {{ __('Previous article') }}</span>
                    <h3>{{ $previous->title }}</h3>
                </a>
            @endif
            @if ($next)
                <a class="pn-next" wire:navigate href="{{ route('blog_article.show', $next->slug) }}" data-cursor="{{ __('Next') }}">
                    <span class="label">{{ __('Next article') }} →</span>
                    <h3>{{ $next->title }}</h3>
                </a>
            @endif
        </nav>
    @endif

    @if ($related_blog_articles->isNotEmpty())
        <section class="sec">
            <div class="sec-head">
                <h2 class="h2 rv">{!! __('Read<br>next') !!}<span class="acc">.</span></h2>
                <a class="more rv" wire:navigate href="{{ route('blog') }}">{{ __('All articles') }} <span>→</span></a>
            </div>
            <div class="rels">
                @foreach ($related_blog_articles as $related_article)
                    <a class="rel rv" wire:navigate href="{{ route('blog_article.show', $related_article->slug) }}" data-cursor="{{ __('Read') }}" data-pan="card-blog-click">
                        <x-blog-cover :article="$related_article" />
                        <h3>{{ $related_article->title }}</h3>
                        <x-article-meta :article="$related_article" />
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <x-contact-band />
</div>
