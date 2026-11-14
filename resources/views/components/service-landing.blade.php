@props([
    'area',
    'pan',
    'mode',
    'name',
    'label',
    'rows',
    'schema_description',
    'pains',
    'offers',
    'scrub',
    'stack',
    'ai',
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
    <x-page-hero :label="$label" :rows="$rows" data-pan="section-impression-services-{{ $pan }}">
        <x-slot:eyebrow>
            <canvas id="svcbg" class="hero-bg" data-fx="svc" data-mode="{{ $mode }}" aria-hidden="true"></canvas>
        </x-slot:eyebrow>

        {{ $intro }}

        <x-slot:actions>
            <a class="more" href="{{ route('services') }}" wire:navigate data-pan="cta-services-{{ $pan }}-back">{{ __('All services') }} <span>→</span></a>
            <a class="cta xl mag hero-cta" href="{{ route('contacts') }}" wire:navigate data-cursor="{{ __('Write to me') }}"
                data-pan="cta-services-{{ $pan }}-consultation"><span>{{ __('Let\'s talk') }}</span><span class="ar">→</span></a>
        </x-slot:actions>
    </x-page-hero>

    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>

    <section class="sec pains-sec">
        <div class="sec-head">
            <h2 class="h2 rv">{!! __('Sound<br>familiar') !!}<span class="acc">?</span></h2>
            <p class="lead rv">{!! __('If you recognize yourself in at least one of these, you are in the right place. <b>We remove them one at a time.</b>') !!}</p>
        </div>
        <ul class="pains">
            @foreach ($pains as $pain)
                <li class="rv">{{ $pain }}</li>
            @endforeach
        </ul>
    </section>

    <section class="sec">
        <div class="sec-head">
            <h2 class="h2 rv">{!! __('What I<br>build') !!}<span class="acc">.</span></h2>
            <a class="more rv" href="{{ route('how_i_work') }}" wire:navigate>{{ __('The method, step by step') }} <span>→</span></a>
        </div>
        <div class="soffer">
            @foreach ($offers as [$offer_title, $offer_lead, $offer_items])
                <div class="so rv">
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

    <section class="sec scrub"><p data-acc="\.$">{{ $scrub }}</p></section>

    <section class="sec">
        <div class="swith">
            <div>
                <h2 class="h2 rv">{{ __('With what') }}<span class="acc">.</span></h2>
                <div class="tags rv">
                    @foreach ($stack as $item)
                        <span>{{ $item }}</span>
                    @endforeach
                </div>
            </div>
            <div class="rv">
                <h3>{{ __('And with AI') }}<span class="acc">.</span></h3>
                <p>{{ $ai }}</p>
            </div>
        </div>
    </section>

    @if ($projects->isNotEmpty())
        <section class="sec tight">
            <div class="sec-head">
                <h2 class="h2 rv">{!! __('Already<br>in use') !!}<span class="acc">.</span></h2>
                <a class="more rv" href="{{ route('projects') }}" wire:navigate>{{ __('All works') }} <span>→</span></a>
            </div>
        </section>

        @foreach ($projects as $project)
            <x-service-project :$project :pan="$loop->first ? 'cta-services-'.$pan.'-project' : 'cta-services-'.$pan.'-case'" />
        @endforeach
    @endif

    <section class="sec">
        <div class="sec-head"><h2 class="h2 rv">{!! __('Frequent<br>questions') !!}<span class="acc">.</span></h2></div>
        <div class="xrows faq" data-fx="faq">
            @foreach ($faqs as [$question, $answer])
                <button class="xrow" type="button">
                    <div class="xrow-h"><span>{{ $question }}</span><em>+</em></div>
                    <div class="xrow-d"><div><p>{{ $answer }}</p></div></div>
                </button>
            @endforeach
        </div>
    </section>

    <section class="sec tight">
        <nav class="tagcloud svc-cloud" aria-label="{{ __('Other services') }}">
            @foreach ($areas as $other_area)
                <a href="{{ route($other_area->getRouteName()) }}" wire:navigate
                    @if ($other_area === $area) class="here" aria-current="page" @endif>{{ $area_names[$other_area->value] }}</a>
            @endforeach
        </nav>
    </section>

    <x-contact-band />
</div>
