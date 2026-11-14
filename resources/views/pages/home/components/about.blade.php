<section class="h-about" id="about" data-sec="about" data-pan="section-impression-cta">
    <div class="portrait">
        <img src="{{ asset('images/about_protrait.webp') }}" alt="{{ config('company.friendly_name') }}" loading="lazy">
    </div>

    <div>
        <h2>{!! __('You work with someone who builds. <em>Not someone who resells.</em>') !!}</h2>
        <p>{{ __('I am :name. I design and develop tailor-made software for companies that stopped adapting to other people\'s tools. I listen, I answer, I follow you: from the first call to the last line of code, and after. No middlemen, no broken telephone.', ['name' => config('company.friendly_name')]) }}</p>
        <p class="facts">{{ __('Over six years of development') }} <span>/</span> {{ __('Lead developer of a custom ERP') }} <span>/</span> {{ __('Direct line, from the first call to after delivery') }}</p>
        <a class="more" href="{{ route('about') }}" wire:navigate data-pan="cta-home-about-more">{{ __('My story') }} <span>→</span></a>
    </div>
</section>
