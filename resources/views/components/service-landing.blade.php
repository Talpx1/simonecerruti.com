@props([
    'area',
    'analyticsSlug',
    'effectMode',
    'name',
    'label',
    'rows',
    'schema_description',
    'pains',
    'offers',
    'closingStatement',
    'technologyStack',
    'aiDescription',
    'projects',
    'faqs',
])

@php
    $areas = collect(\App\Enums\ServiceAreas::cases());
    $area_names = [
        \App\Enums\ServiceAreas::MANAGEMENT_ERP_CRM->value => __('Management software, ERP and CRM'),
        \App\Enums\ServiceAreas::WEB_ECOMMERCE_PLATFORMS->value => __('Websites and e-commerce'),
        \App\Enums\ServiceAreas::CONSULTING_AND_SEO->value => __('Consulting and SEO'),
    ];
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Service',
                'name' => $name,
                'description' => $schema_description,
                'serviceType' => $name,
                'areaServed' => 'IT',
                'provider' => ['@type' => 'Person', 'name' => config('company.name'), 'url' => url('/')],
            ],
            [
                '@type' => 'FAQPage',
                'mainEntity' => collect($faqs)->map(fn (array $faq): array => [
                    '@type' => 'Question',
                    'name' => $faq[0],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq[1]],
                ])->all(),
            ],
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => __('Services'), 'item' => route('services')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => $name],
                ],
            ],
        ],
    ];
@endphp

<div>
    <x-page-hero :label="$label" :rows="$rows" data-pan="section-impression-services-{{ $analyticsSlug }}">
        <x-slot:eyebrow>
            <canvas id="service-landing-hero-canvas" class="page-hero-background" data-effect="service-landing-hero" data-mode="{{ $effectMode }}" aria-hidden="true"></canvas>
        </x-slot:eyebrow>

        {{ $intro }}

        <x-slot:actions>
            <a class="button-outline-link" href="{{ route('services') }}" wire:navigate data-pan="cta-services-{{ $analyticsSlug }}-back">{{ __('All services') }} <span>→</span></a>
            <a class="button-primary button-large magnetic-hover home-hero-cta" href="{{ route('contacts') }}" wire:navigate data-cursor="{{ __('Write to me') }}"
                data-pan="cta-services-{{ $analyticsSlug }}-consultation"><span>{{ __('Let\'s talk') }}</span><span class="button-arrow">→</span></a>
        </x-slot:actions>
    </x-page-hero>

    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>

    <section class="section service-pains-section">
        <div class="section-header">
            <h2 class="section-title reveal">{!! __('Sound<br>familiar') !!}<span class="text-accent">?</span></h2>
            <p class="section-lead reveal">{!! __('If you recognize yourself in at least one of these, you are in the right place. <b>We remove them one at a time.</b>') !!}</p>
        </div>
        <ul class="service-pains">
            @foreach ($pains as $pain)
                <li class="reveal">{{ $pain }}</li>
            @endforeach
        </ul>
    </section>

    <section class="section">
        <div class="section-header">
            <h2 class="section-title reveal">{!! __('What I<br>build') !!}<span class="text-accent">.</span></h2>
            <a class="button-outline-link reveal" href="{{ route('how_i_work') }}" wire:navigate>{{ __('The method, step by step') }} <span>→</span></a>
        </div>
        <div class="service-offer">
            @foreach ($offers as [$offer_title, $offer_lead, $offer_items])
                <div class="service-offer-item reveal">
                    <h3>{{ $offer_title }}</h3>
                    <p>{{ $offer_lead }}</p>
                    <ul>
                        @foreach ($offer_items as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>

    <section class="section scroll-lit"><p data-accent-pattern="\.$">{{ $closingStatement }}</p></section>

    <section class="section">
        <div class="service-stack">
            <div>
                <h2 class="section-title reveal">{{ __('With what') }}<span class="text-accent">.</span></h2>
                <div class="chip-list reveal">
                    @foreach ($technologyStack as $item)
                        <span>{{ $item }}</span>
                    @endforeach
                </div>
            </div>
            <div class="reveal">
                <h3>{{ __('And with AI') }}<span class="text-accent">.</span></h3>
                <p>{{ $aiDescription }}</p>
            </div>
        </div>
    </section>

    @if ($projects->isNotEmpty())
        <section class="section section--tight">
            <div class="section-header">
                <h2 class="section-title reveal">{!! __('Already<br>in use') !!}<span class="text-accent">.</span></h2>
                <a class="button-outline-link reveal" href="{{ route('projects') }}" wire:navigate>{{ __('All works') }} <span>→</span></a>
            </div>
        </section>

        @foreach ($projects as $project)
            <x-service-project :$project :analytics-name="$loop->first ? 'cta-services-'.$analyticsSlug.'-project' : 'cta-services-'.$analyticsSlug.'-case'" />
        @endforeach
    @endif

    <section class="section">
        <div class="section-header"><h2 class="section-title reveal">{!! __('Frequent<br>questions') !!}<span class="text-accent">.</span></h2></div>
        <div class="accordion service-faq" data-effect="faq">
            @foreach ($faqs as [$question, $answer])
                <button class="accordion-item" type="button">
                    <div class="accordion-item-head"><span>{{ $question }}</span><em>+</em></div>
                    <div class="accordion-item-body"><div><p>{{ $answer }}</p></div></div>
                </button>
            @endforeach
        </div>
    </section>

    <section class="section section--tight">
        <nav class="tag-cloud service-tagcloud" aria-label="{{ __('Other services') }}">
            @foreach ($areas as $other_area)
                <a href="{{ route($other_area->getRouteName()) }}" wire:navigate
                    @if ($other_area === $area) class="is-current" aria-current="page" @endif>{{ $area_names[$other_area->value] }}</a>
            @endforeach
        </nav>
    </section>

    <x-contact-band />
</div>
