@php
    $name_parts = explode(' ', config()->string('company.friendly_name'));

    $canvas_config = [
        'locale' => app()->getLocale(),
        'arch' => [
            'core' => __('Your software'),
            'core_sub' => __('rules · flows · automations'),
            'modules' => [
                __('Orders'),
                __('Warehouse'),
                __('Customers'),
                __('Excel'),
                __('Courier · API'),
                __('Mail'),
                __('Reports'),
                __('Invoices'),
            ],
        ],
        'ui' => [
            'app' => __('Your ERP'),
            'nav' => [__('Dashboard'), __('Orders'), __('Warehouse'), __('Customers'), __('Invoices'), __('Reports')],
            'user' => $name_parts[0].' '.mb_substr(end($name_parts), 0, 1).'.',
            'greeting' => __('Good morning,'),
            'first_name' => $name_parts[0],
            'loading' => __('Loading data…'),
            'all_good' => __('Everything under control'),
            'search' => __('Search orders, customers…'),
            'new_order' => __('+ New order'),
            'currency' => '€',
            'kpis' => [
                ['label' => __('Orders today'), 'sub' => __('+12% compared to yesterday')],
                ['label' => __('To ship'), 'sub' => __('by 5:00 PM')],
                ['label' => __('Revenue this month'), 'sub' => __('+8% on last month')],
                ['label' => __('Low stock'), 'sub' => __('reorder suggested')],
            ],
            'chart_title' => __('Orders · last 14 days'),
            'orders_title' => __('Latest orders'),
            'rows' => [
                ['id' => '#1042', 'who' => 'Bottega Sardi', 'status' => __('In preparation')],
                ['id' => '#1041', 'who' => 'Hotel Miramonti', 'status' => __('Shipped')],
                ['id' => '#1040', 'who' => 'Caffè Centrale', 'status' => __('Shipped')],
                ['id' => '#1039', 'who' => 'Panificio Bassi', 'status' => __('Delivered')],
                ['id' => '#1038', 'who' => 'Studio Neri', 'status' => __('Delivered')],
            ],
            'incoming' => [
                'id' => '#1043',
                'who' => __('From mail · :client', ['client' => 'Studio Riva']),
                'status' => __('New (status)'),
            ],
        ],
        'toast' => [
            'title' => __('Order #1043 created from the mail'),
            'sub' => __('Warehouse updated · Invoice ready'),
        ],
    ];
@endphp

<div data-fx="home" data-config="{{ json_encode($canvas_config, JSON_UNESCAPED_UNICODE) }}">
    <canvas id="gl" aria-hidden="true"></canvas>

    @include('pages.home.components.hero')
    @include('pages.home.components.marquee')
    @include('pages.home.components.story')

    @if ($works !== [])
        @include('pages.home.components.works')
    @endif

    @include('pages.home.components.services')
    @include('pages.home.components.manifesto')
    @include('pages.home.components.about')

    <div data-sec="contact">
        <x-contact-band />
    </div>
</div>
