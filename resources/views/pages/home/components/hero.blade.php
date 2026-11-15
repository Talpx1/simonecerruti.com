<section class="home-hero" id="top" data-section="hero" data-pan="section-impression-hero">
    <div class="home-hero-top">
        <p>{!! __('<b>Management software, ERPs, websites, e-commerce and apps.</b> Built from scratch, tailor-made, no shortcuts. If you are looking for a template, you are at the wrong address.') !!}</p>
    </div>

    <div class="home-hero-main">
        <h1 class="home-hero-title" aria-label="{{ __('I build the software you were looking for…') }}">
            <span class="page-hero-title-line" data-variable-letters>{{ __('I build the software') }}</span>
            <span class="page-hero-title-line" data-variable-letters>{{ __('you were looking for…') }}</span>
        </h1>

        <div class="home-hero-sub">
            <p class="home-hero-tagline">{!! __('…and what you did not even know you <em>wanted.</em>') !!}</p>
            <a class="button-primary button-large magnetic-hover home-hero-cta" href="{{ route('contacts') }}" wire:navigate
                data-cursor="{{ __('Write to me') }}" data-pan="cta-hero-contacts"><span>{{ __('Let\'s talk') }}</span><span class="button-arrow">→</span></a>
        </div>

        <div class="home-hero-foot">
            <nav class="home-hero-links" aria-label="{{ __('What I build') }}">
                <a href="{{ route($service_routes['erp']) }}" wire:navigate data-pan="cta-home-door-erp">{{ __('Management software') }} <span>→</span></a>
                <a href="{{ route($service_routes['web']) }}" wire:navigate data-pan="cta-home-door-websites">{{ __('Websites') }} <span>→</span></a>
                <a href="{{ route($service_routes['web']) }}" wire:navigate data-pan="cta-home-door-ecommerce">{{ __('E-commerce') }} <span>→</span></a>
                <a href="{{ route('services') }}" wire:navigate data-pan="cta-home-door-apps">{{ __('Apps and platforms') }} <span>→</span></a>
                <a href="{{ route('projects') }}" wire:navigate data-pan="cta-home-door-works">{{ __('Works') }} <span>→</span></a>
            </nav>
            <span class="home-hero-scroll-cue"><i></i>{{ __('Scroll. See how it is done.') }}</span>
        </div>
    </div>
</section>
