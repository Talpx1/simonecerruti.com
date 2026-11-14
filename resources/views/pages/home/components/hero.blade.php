<section class="hero" id="top" data-sec="hero" data-pan="section-impression-hero">
    <div class="hero-top">
        <p>{!! __('<b>Management software, ERPs, websites, e-commerce and apps.</b> Built from scratch, tailor-made, no shortcuts. If you are looking for a template, you are at the wrong address.') !!}</p>
    </div>

    <div class="hero-main">
        <h1 class="h1" aria-label="{{ __('I build the software you were looking for…') }}">
            <span class="row" data-split>{{ __('I build the software') }}</span>
            <span class="row" data-split>{{ __('you were looking for…') }}</span>
        </h1>

        <div class="hero-sub">
            <p class="it">{!! __('…and what you did not even know you <em>wanted.</em>') !!}</p>
            <a class="cta xl mag hero-cta" href="{{ route('contacts') }}" wire:navigate
                data-cursor="{{ __('Write to me') }}" data-pan="cta-hero-contacts"><span>{{ __('Let\'s talk') }}</span><span class="ar">→</span></a>
        </div>

        <div class="hero-foot">
            <nav class="doors" aria-label="{{ __('What I build') }}">
                <a href="{{ route($service_routes['erp']) }}" wire:navigate data-pan="cta-home-door-erp">{{ __('Management software') }} <span>→</span></a>
                <a href="{{ route($service_routes['web']) }}" wire:navigate data-pan="cta-home-door-websites">{{ __('Websites') }} <span>→</span></a>
                <a href="{{ route($service_routes['web']) }}" wire:navigate data-pan="cta-home-door-ecommerce">{{ __('E-commerce') }} <span>→</span></a>
                <a href="{{ route('services') }}" wire:navigate data-pan="cta-home-door-apps">{{ __('Apps and platforms') }} <span>→</span></a>
                <a href="{{ route('projects') }}" wire:navigate data-pan="cta-home-door-works">{{ __('Works') }} <span>→</span></a>
            </nav>
            <span class="scroll-cue"><i></i>{{ __('Scroll. See how it is done.') }}</span>
        </div>
    </div>
</section>
