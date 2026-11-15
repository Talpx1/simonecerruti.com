@use('App\Enums\ServiceAreas')

@php
    $names = [
        'management' => __('Management systems · ERP · CRM'),
        'web' => __('Websites · E-commerce · Web apps'),
        'consulting' => __('a 15-minute consultation'),
    ];

    $goals = [
        [__('Manage the company better'), 'management'],
        [__('Sell online'), 'web'],
        [__('Digitize a process'), 'management'],
        [__('Build or rebuild a website'), 'web'],
        [__('Build a platform'), 'web'],
        [__('I do not know yet'), 'consulting'],
    ];

    $areas = [
        [
            'key' => 'management',
            'area' => ServiceAreas::MANAGEMENT_ERP_CRM,
            'title' => __('The company in<br>one place.'),
            'label' => __('Management systems · ERP · CRM'),
            'cta' => __('Let\'s talk about your management software'),
            'more' => __('How I build them'),
            'points' => [
                __('Orders, inventory, customers, invoices and internal processes in a single system'),
                __('Automations that remove data entry and errors'),
                __('Real-time data to decide before the others'),
                __('Goodbye to scattered Excel sheets and double data entry'),
            ],
            'analytics_name' => 'cta-services-custom-software-development',
        ],
        [
            'key' => 'web',
            'area' => ServiceAreas::WEB_ECOMMERCE_PLATFORMS,
            'title' => __('The web<br>that sells.'),
            'label' => __('Websites · E-commerce · Web apps'),
            'cta' => __('Build your website'),
            'more' => __('Inside the web service'),
            'points' => [
                __('From showcase site to e-commerce, up to the tailor-made web app'),
                __('Fast and SEO-optimized from the code up'),
                __('Scalable: it grows together with your business'),
                __('Fluid navigation, an experience that stays in mind'),
            ],
            'analytics_name' => 'cta-services-area-2',
        ],
        [
            'key' => 'consulting',
            'area' => ServiceAreas::CONSULTING_AND_SEO,
            'title' => __('Strategy first.<br><em>Then the code.</em>'),
            'label' => __('Consulting · SEO'),
            'cta' => __('Request a consultation'),
            'more' => __('How I work on strategy and SEO'),
            'points' => [
                __('We figure out what is really needed, before writing a single line'),
                __('Independent technology choices, with no ties to vendors'),
                __('Technical SEO done right to get you found on Google'),
                __('Support for your team, when needed'),
            ],
            'analytics_name' => 'cta-services-area-3',
        ],
    ];

    $comparison = [
        [__('Flexibility'), __('You adapt to the software.'), __('The software adapts to you.')],
        [__('Code'), __('Rented. Forever.'), __('100% yours. No fees.')],
        [__('Control'), __('The vendor decides.'), __('You decide, with whoever builds it.')],
        [__('Competition'), __('Same as everyone else\'s.'), __('An edge the others do not have.')],
    ];

    $hub_config = [
        'verdict' => __('For “:goal” you need <span>:service</span>.'),
        'names' => $names,
        'channels' => ['management' => 0, 'web' => 1, 'consulting' => 2],
    ];
@endphp

<div>
    <x-page-hero :label="__('Services: what do you want to solve?')" :rows="[__('What do you want'), __('to solve?')]"
        data-pan="section-impression-services-hero">
        <x-slot:eyebrow>
            <canvas id="services-routes-canvas" class="page-hero-background" data-effect="services-routes" aria-hidden="true"
                data-labels="{{ json_encode([__('Management systems · ERP · CRM'), __('Websites · E-commerce · Web apps'), __('Consulting · SEO')], JSON_UNESCAPED_UNICODE) }}"></canvas>
        </x-slot:eyebrow>

        <p>{!! __('Tailor-made software, web and AI. Pick your goal: I take you straight to the right service, <em>no beating around the bush.</em>') !!}</p>

        <x-slot:actions>
            <a class="button-primary button-large magnetic-hover home-hero-cta" href="{{ route('contacts') }}" wire:navigate data-cursor="{{ __('Write to me') }}"
                data-pan="cta-services-contacts"><span>{{ __('Let\'s talk') }}</span><span class="button-arrow">→</span></a>
        </x-slot:actions>
    </x-page-hero>

    <div data-effect="services-hub" data-config="{{ json_encode($hub_config, JSON_UNESCAPED_UNICODE) }}">
        <section class="section section--tight" id="goals">
            <div class="services-goals" role="group" aria-label="{{ __('Your goal') }}">
                @foreach ($goals as [$goal_label, $goal_target])
                    <button type="button" class="services-goal" data-to="{{ $goal_target }}"><span>{{ $goal_label }}</span></button>
                @endforeach
            </div>
            <div class="services-verdict" aria-live="polite">
                <p id="services-verdict">{!! __('Pick a goal above. <span>I take care of the rest.</span>') !!}</p>
                <a class="button-outline-link" id="services-verdict-link" href="#area-management" hidden>{{ __('Go to the service') }} <span>↓</span></a>
            </div>
        </section>

        <section class="section section--tight services-cards" id="areas">
            @foreach ($areas as $item)
                @php
                    $example = $area_projects->get($item['area']->value);
                    $landing_url = route($item['area']->getRouteName());
                @endphp
                <article class="services-card" id="area-{{ $item['key'] }}">
                    <div>
                        <h3><a href="{{ $landing_url }}" wire:navigate>{!! $item['title'] !!}</a></h3>
                        <span class="services-card-area">{{ $item['label'] }}</span>
                        <div class="services-card-actions">
                            <a class="button-primary magnetic-hover" href="{{ route('contacts') }}" wire:navigate
                                data-pan="cta-services-{{ $item['key'] }}-consultation"><span>{{ $item['cta'] }}</span><span class="button-arrow">→</span></a>
                            <a class="button-outline-link" href="{{ $landing_url }}" wire:navigate
                                data-pan="cta-services-{{ $item['key'] }}-more">{{ $item['more'] }} <span>→</span></a>
                        </div>
                    </div>
                    <div class="services-examples">
                        <ul>
                            @foreach ($item['points'] as $point)
                                <li>{{ $point }}</li>
                            @endforeach
                        </ul>
                        @if ($example)
                            <a class="services-example" href="{{ route('project.show', $example->slug) }}" wire:navigate
                                data-cursor="{{ __('Open page') }}" data-pan="{{ $item['analytics_name'] }}">
                                <span class="services-example-thumbnail" aria-hidden="true"><i>{{ str($example->title)->substr(0, 1)->upper() }}</i></span>
                                <span class="services-example-text"><b>{{ $example->title }}</b><span>{{ str($example->short_description_or_excerpt)->limit(120) }}</span></span>
                                <em>→</em>
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </section>

        <section class="section services-compare" data-mode="template" id="services-compare">
            <div class="services-compare-head">
                <h2 class="section-title">{!! __('Why<br>tailor-made') !!}<span class="text-accent">.</span></h2>
                <div class="segmented-toggle" role="group" aria-label="{{ __('Compare') }}">
                    <button type="button" class="is-active" data-mode="template">{{ __('Template') }}</button>
                    <button type="button" data-mode="custom">{{ __('Tailor-made') }}</button>
                </div>
            </div>
            @foreach ($comparison as [$flip_key, $flip_template, $flip_custom])
                <div class="services-compare-row">
                    <span class="services-compare-key">{{ $flip_key }}</span>
                    <div class="services-compare-value"><span class="services-compare-value-template">{{ $flip_template }}</span><span class="services-compare-value-custom">{{ $flip_custom }}</span></div>
                </div>
            @endforeach
        </section>
    </div>

    <section class="section scroll-lit"><p data-accent-pattern="\.$">{{ __('AI inside your products: assistants, smart automations, search across your data. And in my work, for more robust software. Never in place of quality.') }}</p></section>

    <x-contact-band />
</div>
