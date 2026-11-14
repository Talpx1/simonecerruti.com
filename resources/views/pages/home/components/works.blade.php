<section class="works" id="works" data-sec="works" data-pan="section-impression-projects">
    <div class="works-pin">
        <div class="w-head">
            <h2 class="w-title">{{ __('Works') }}<span>.</span></h2>
            <div>
                <p>{{ __('Anyone can make promises. These are delivered and in use.') }}</p>
                <a class="more" href="{{ route('projects') }}" wire:navigate data-pan="cta-hero-projects">{{ __('All works') }} <span>→</span></a>
            </div>
        </div>

        <div class="w-grid">
            <ol class="w-list">
                @foreach ($works as $work)
                    <li>
                        <button type="button" class="w-it" data-i="{{ $loop->index }}">
                            <span class="nm">{{ $work['title'] }}</span>
                            @if ($work['kind'] !== '')
                                <span class="kd">{{ $work['kind'] }}</span>
                            @endif
                        </button>
                    </li>
                @endforeach
            </ol>

            <div class="w-stage">
                <div class="screen">
                    @foreach ($works as $work)
                        <div class="shot" data-i="{{ $loop->index }}">
                            <div class="after">
                                @if ($work['image_url'])
                                    <img src="{{ $work['image_url'] }}" alt="{{ $work['title'] }}" loading="lazy">
                                @else
                                    <span class="ghost" aria-hidden="true">{{ mb_strtoupper(mb_substr($work['title'], 0, 1)) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="w-nows">
                    @foreach ($works as $work)
                        <div class="w-now" data-i="{{ $loop->index }}">
                            @if ($work['summary_html'])
                                <div>{!! $work['summary_html'] !!}</div>
                            @else
                                <span></span>
                            @endif
                            <a href="{{ $work['url'] }}" wire:navigate
                                data-pan="cta-home-work-open-{{ $loop->iteration }}">{{ __('Open the work') }} <span>→</span></a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
