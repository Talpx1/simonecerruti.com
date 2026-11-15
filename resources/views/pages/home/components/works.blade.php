<section class="home-works" id="works" data-section="works" data-pan="section-impression-projects">
    <div class="home-works-stage">
        <div class="home-works-head">
            <h2 class="home-works-title">{{ __('Works') }}<span>.</span></h2>
            <div>
                <p>{{ __('Anyone can make promises. These are delivered and in use.') }}</p>
                <a class="button-outline-link" href="{{ route('projects') }}" wire:navigate data-pan="cta-hero-projects">{{ __('All works') }} <span>→</span></a>
            </div>
        </div>

        <div class="home-works-grid">
            <ol class="home-works-list">
                @foreach ($works as $work)
                    <li>
                        <button type="button" class="home-works-item">
                            <span class="home-works-item-name">{{ $work->title }}</span>
                            @if ($work->kind !== '')
                                <span class="home-works-item-kind">{{ $work->kind }}</span>
                            @endif
                        </button>
                    </li>
                @endforeach
            </ol>

            <div class="home-works-preview">
                <div class="before-after-screen">
                    @foreach ($works as $work)
                        <div class="home-works-shot">
                            <div class="before-after-screen-after">
                                @if ($work->image_url)
                                    <img src="{{ $work->image_url }}" alt="{{ $work->title }}" loading="lazy">
                                @else
                                    <span class="before-after-screen-watermark" aria-hidden="true">{{ mb_strtoupper(mb_substr($work->title, 0, 1)) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="home-works-captions">
                    @foreach ($works as $work)
                        <div class="home-works-caption">
                            @if ($work->summary_html)
                                <div>{!! $work->summary_html !!}</div>
                            @else
                                <span></span>
                            @endif
                            <a href="{{ $work->url }}" wire:navigate
                                data-pan="cta-home-work-open-{{ $loop->iteration }}">{{ __('Open the work') }} <span>→</span></a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
