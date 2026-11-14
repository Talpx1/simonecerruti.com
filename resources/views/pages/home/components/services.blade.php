@php
    $service_rows = [
        ['route' => $service_routes['erp'], 'pan' => 'erp', 'title' => __('Management software, ERP and CRM'), 'text' => __('Orders, warehouse, customers and invoices in one place, built on your process. The day after, you close Excel.')],
        ['route' => $service_routes['web'], 'pan' => 'websites', 'title' => __('Websites'), 'text' => __('Your tone, not a template\'s. Fast, pixel-perfect, made to get you chosen over the others.')],
        ['route' => $service_routes['web'], 'pan' => 'ecommerce', 'title' => __('E-commerce'), 'text' => __('Your catalog, your rules. From click to order without friction, ready to hold up when sales grow.')],
        ['route' => $service_routes['web'], 'pan' => 'apps', 'title' => __('Apps and platforms'), 'text' => __('Your idea, built to scale. With AI inside where it really helps, not where it looks good.')],
    ];
@endphp

<section class="svc" id="services" data-sec="svc" data-pan="section-impression-services">
    <div class="svc-head">
        <h2>{!! __('What I<br>build') !!}<span style="color: var(--acc)">.</span></h2>
        <p>{{ __('One rule: nothing off the shelf. Choose where to start, I take care of the rest.') }}</p>
    </div>

    <div class="rows">
        @foreach ($service_rows as $service_row)
            <a class="srow" href="{{ route($service_row['route']) }}" wire:navigate data-cursor="{{ __('Discover') }}"
                data-pan="cta-home-service-{{ $service_row['pan'] }}">
                <div class="srow-h"><span>{{ $service_row['title'] }}</span><em>→</em></div>
                <div class="srow-d"><div><p>{{ $service_row['text'] }}</p></div></div>
            </a>
        @endforeach
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; gap: 24px; flex-wrap: wrap; margin-top: 26px">
        <p class="svc-foot" style="margin: 0">{{ __('Whatever you are looking for, I build it exactly the way you picture it. Not sure what you need yet?') }}
            <a href="{{ route('contacts') }}" wire:navigate data-pan="cta-home-services-call">{{ __('15 minutes are enough to figure it out.') }}</a></p>
        <a class="more" href="{{ route('services') }}" wire:navigate data-pan="cta-home-services">{{ __('All services') }} <span>→</span></a>
    </div>
</section>
