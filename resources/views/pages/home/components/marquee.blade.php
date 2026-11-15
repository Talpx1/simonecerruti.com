@php
    $first_row = [__('Management software'), __('ERP'), __('CRM'), __('Websites'), __('E-commerce'), __('SaaS')];
    $second_row = [__('Web platforms'), __('PWA'), __('Apps'), __('Automations')];
@endphp

<section class="home-marquee" data-section="marquee" aria-label="{{ __('What I build') }}">
    <div class="home-marquee-row" data-direction="1">
        <div class="home-marquee-track">
            @foreach (range(1, 2) as $copy)
                @foreach ($first_row as $word)
                    <span>{{ $word }}</span><i></i>
                @endforeach
            @endforeach
        </div>
    </div>
    <div class="home-marquee-row home-marquee-row--outline" data-direction="-1">
        <div class="home-marquee-track">
            @foreach (range(1, 2) as $copy)
                @foreach ($second_row as $word)
                    <span>{{ $word }}</span><i></i>
                @endforeach
                <em>{{ __('and whatever you have in mind') }}</em><i></i>
            @endforeach
        </div>
    </div>
</section>
