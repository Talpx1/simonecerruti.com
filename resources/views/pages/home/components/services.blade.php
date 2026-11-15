@php
    $service_rows = [
        ['route' => $service_routes['erp'], 'analytics_name' => 'erp', 'title' => __('Management software, ERP and CRM'), 'text' => __('Orders, warehouse, customers and invoices in one place, built on your process. The day after, you close Excel.')],
        ['route' => $service_routes['web'], 'analytics_name' => 'websites', 'title' => __('Websites'), 'text' => __('Your tone, not a template\'s. Fast, pixel-perfect, made to get you chosen over the others.')],
        ['route' => $service_routes['web'], 'analytics_name' => 'ecommerce', 'title' => __('E-commerce'), 'text' => __('Your catalog, your rules. From click to order without friction, ready to hold up when sales grow.')],
        ['route' => $service_routes['web'], 'analytics_name' => 'apps', 'title' => __('Apps and platforms'), 'text' => __('Your idea, built to scale. With AI inside where it really helps, not where it looks good.')],
    ];
@endphp

<section class="home-services" id="services" data-section="services" data-pan="section-impression-services">
    <div class="home-services-head">
        <h2>{!! __('What I<br>build') !!}<span style="color: var(--site-color-accent)">.</span></h2>
        <p>{{ __('One rule: nothing off the shelf. Choose where to start, I take care of the rest.') }}</p>
    </div>

    <div class="home-services-list">
        @foreach ($service_rows as $service_row)
            <a class="home-services-item" href="{{ route($service_row['route']) }}" wire:navigate data-cursor="{{ __('Discover') }}"
                data-pan="cta-home-service-{{ $service_row['analytics_name'] }}">
                <div class="home-services-item-head"><span>{{ $service_row['title'] }}</span><em>→</em></div>
                <div class="home-services-item-body"><div><p>{{ $service_row['text'] }}</p></div></div>
            </a>
        @endforeach
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; gap: 24px; flex-wrap: wrap; margin-top: 26px">
        <p class="home-services-foot" style="margin: 0">{{ __('Whatever you are looking for, I build it exactly the way you picture it. Not sure what you need yet?') }}
            <a href="{{ route('contacts') }}" wire:navigate data-pan="cta-home-services-call">{{ __('15 minutes are enough to figure it out.') }}</a></p>
        <a class="button-outline-link" href="{{ route('services') }}" wire:navigate data-pan="cta-home-services">{{ __('All services') }} <span>→</span></a>
    </div>
</section>
