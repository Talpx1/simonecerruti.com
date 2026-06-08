@php
    // Highlight markup for headings. The :tag/:close_tag replacements inject the
    // <x-highlighted-text> component tag, which Blade::render() compiles at runtime.
    // This MUST stay in a @php block: a literal <x-...> tag written inline in {!! !!}
    // is rewritten by Blade's component compiler at *compile* time and breaks.
    $hl_tags = ['tag' => '<x-highlighted-text>', 'close_tag' => '</x-highlighted-text>'];
@endphp

<div>
    {{-- ============================== HERO ============================== --}}
    <section data-pan="section-impression-services-hero" class="relative overflow-hidden pt-16 lg:pt-24 pb-16 lg:pb-20">
        <div class="relative max-w-7xl mx-auto px-8 lg:px-14">

            <x-app-logo weight="thin"
                class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 w-[min(55vw,560px)] opacity-5 hidden md:block" />

            <div class="relative z-10">
                <x-eyebrow>{{ __('Services') }}</x-eyebrow>

                <h1
                    class="mt-6 font-black uppercase leading-none tracking-tighter text-light text-5xl sm:text-6xl md:text-7xl lg:text-8xl xl:text-9xl max-w-[14ch]">
                    {!! Blade::render(__('What do you want to<br>:tag solve? :close_tag', $hl_tags)) !!}
                </h1>

                <p class="mt-7 text-light/55 text-lg lg:text-xl font-light leading-relaxed max-w-2xl">
                    {{ __('Pick your goal: I\'ll take you to the right service, explained in plain words. Software, web and AI — tailor-made, built around your processes.') }}
                </p>

                @php
                    $goals = [
                        [__('Manage the company better'), '#area-01'],
                        [__('Sell online'), '#area-02'],
                        [__('Digitize a process'), '#area-01'],
                        [__('Build or rebuild a website'), '#area-02'],
                        [__('Build a platform'), '#area-02'],
                        [__("I don't know yet"), '#area-03'],
                    ];
                @endphp

                <div class="mt-12 lg:mt-14 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($goals as $i => [$label, $href])
                        <a href="{{ $href }}" @unless (str_starts_with($href, '#')) wire:navigate @endunless
                            class="group flex items-center justify-between gap-4 border border-light/15 p-6 lg:p-7 min-h-[132px] transition-colors duration-300 hover:bg-light hover:text-dark hover:border-light">
                            <span class="block">
                                <span
                                    class="block text-xs text-light/40 transition-colors group-hover:text-dark/50">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                <span
                                    class="mt-4 block font-bold uppercase text-xl leading-tight tracking-tight">{{ $label }}</span>
                            </span>
                            <x-ri-arrow-right-long-line
                                class="w-5 shrink-0 opacity-50 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-1.5" />
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== AREE ============================== --}}
    <section id="aree" data-reveal class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14 space-y-20 lg:space-y-32">

            {{-- Area 01 — Management software · ERP · CRM --}}
            <x-pages::services.components.feature-row id="area-01" :area="__('Area 01 — Management software · ERP · CRM')"
                :lead="__('Orders, inventory, customers, invoicing and internal processes in a single system, tailored to how you actually work.')"
                :bullets="[
                    __('Workflow automation: less manual work, fewer human errors.'),
                    __('Real-time data: faster, better-informed decisions.'),
                    __('Goodbye to scattered spreadsheets and double data entry.'),
                ]"
                :button-label="__('Discover tailor-made management software')" :button-href="route('services.management_erp_crm')">
                <x-slot:media>
                    <x-pages::services.components.mock-dashboard />
                </x-slot:media>
                <x-slot:heading>
                    {!! Blade::render(__('A single place to<br>:tag run :close_tag the company', $hl_tags)) !!}
                </x-slot:heading>
                @if ($area_one_project)
                    <x-slot:contextual>
                        <x-pages::services.components.contextual-link :href="route('project.show', $area_one_project->slug)"
                            :prefix="__('Project:')" :label="$area_one_project->title" pan="cta-services-custom-software-development" />
                    </x-slot:contextual>
                @endif
            </x-pages::services.components.feature-row>

            {{-- Area 02 — Websites · E-commerce · Platforms · Web apps (flipped) --}}
            <x-pages::services.components.feature-row id="area-02" flip
                :area="__('Area 02 — Websites · E-commerce · Platforms · Web apps')"
                :lead="__('From a showcase site to a tailor-made web app, all the way to an e-commerce that truly converts.')"
                :bullets="[
                    __('Fast and SEO-optimized from the very first line of code.'),
                    __('Scalable: it grows together with your business.'),
                    __('Immersive experience and fluid navigation.'),
                ]"
                :button-label="__('Build your website or e-commerce')" :button-href="route('services.web_ecommerce_platforms')">
                <x-slot:media>
                    <x-pages::services.components.mock-shop />
                </x-slot:media>
                <x-slot:heading>
                    {!! Blade::render(__('The web that<br>:tag sells :close_tag and scales', $hl_tags)) !!}
                </x-slot:heading>
                @if ($area_two_project)
                    <x-slot:contextual>
                        <x-pages::services.components.contextual-link :href="route('project.show', $area_two_project->slug)"
                            :prefix="__('Project:')" :label="$area_two_project->title" pan="cta-services-area-2" />
                    </x-slot:contextual>
                @endif
            </x-pages::services.components.feature-row>

            {{-- Area 03 — Consulting · SEO --}}
            <x-pages::services.components.feature-row id="area-03" :area="__('Area 03 — Consulting · SEO')"
                :lead="__('Let\'s figure out together what you really need — before writing a single line of code.')"
                :bullets="[
                    __('Independent technology choices, with no ties to a single vendor.'),
                    __('SEO consulting paired with the website: solid technical foundations to rank you on Google.'),
                    __('Support alongside your team, when needed.'),
                ]"
                :button-label="__('Request a consultation')" :button-href="route('services.consulting_and_seo')">
                <x-slot:media>
                    <x-pages::services.components.mock-seo />
                </x-slot:media>
                <x-slot:heading>
                    {!! Blade::render(__('First of all,<br>the :tag strategy :close_tag', $hl_tags)) !!}
                </x-slot:heading>
                @if ($area_three_article)
                    <x-slot:contextual>
                        <x-pages::services.components.contextual-link
                            :href="route('blog_article.show', $area_three_article->slug)" :prefix="__('Read:')"
                            :label="$area_three_article->title" pan="cta-services-area-3" />
                    </x-slot:contextual>
                @endif
            </x-pages::services.components.feature-row>

        </div>
    </section>

    {{-- ============================== VALORI ============================== --}}
    <section data-reveal class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <x-eyebrow>{{ __('Why bespoke') }}</x-eyebrow>

            <h2
                class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[18ch]">
                {{ __('The advantages of software tailored to you') }}
            </h2>

            @php
                $values = [
                    [__('Full flexibility'), __('You don\'t adapt to the software: it shapes itself around your needs and grows with your company, one feature at a time.')],
                    [__('100% your code'), __('You own the code. No lifelong fees, no lock-in: if you change provider, the software stays yours.')],
                    [__('Full control'), __('Features, scalability, look and functionality: you decide, in direct contact with whoever builds the product.')],
                    [__('Competitive edge'), __('Tailored technology and innovation: more efficiency and differentiation to stand out in a crowded market.')],
                ];
            @endphp

            <div class="mt-12 lg:mt-14 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 border-t border-light/15">
                @foreach ($values as $i => [$title, $description])
                    <div @class([
                        'p-7 lg:p-8 border-b border-light/15',
                        'md:border-l' => in_array($i, [1, 3], true),
                        'lg:border-l' => $i === 2,
                    ])>
                        <div class="text-xs text-light/40 mb-5">
                            {{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</div>
                        <h3 class="font-black uppercase tracking-tight text-light text-xl lg:text-2xl mb-3">{{ $title }}</h3>
                        <p class="text-light/55 text-sm leading-relaxed font-light">{{ $description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== AI BAND ============================== --}}
    <x-pages::services.components.ai-band :eyebrow="__('With AI')"
        :lead="__('I build AI into the software I create — and I use it every day to develop better, more solid and reliable apps.')"
        :points="[
            [__('AI built into your products.'), __('Assistants, smart automations, semantic search and data analysis, inside your management software or platform.')],
            [__('AI-assisted development.'), __('I build more robust, reliable software — without compromising on code quality.')],
        ]">
        {!! __('Artificial intelligence,<br>inside your products') !!}
    </x-pages::services.components.ai-band>

    {{-- ============================== CASI REALI ============================== --}}
    @if ($cards_projects->isNotEmpty() || $cards_article)
        <section data-reveal class="border-t border-light/15 py-20 lg:py-28">
            <div class="max-w-7xl mx-auto px-8 lg:px-14">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <x-eyebrow>{{ __('Real cases & insights') }}</x-eyebrow>
                        <h2
                            class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                            {!! __('Results that<br>speak for themselves') !!}
                        </h2>
                    </div>
                    <a wire:navigate href="{{ route('projects') }}"
                        class="group inline-flex shrink-0 items-center gap-2 text-xs uppercase tracking-[0.2em] text-light/55 transition-colors hover:text-light">
                        {{ __('All projects') }}
                        <x-ri-arrow-right-long-line
                            class="w-4 transition-transform duration-300 group-hover:translate-x-1" />
                    </a>
                </div>

                <div class="mt-12 lg:mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                    @foreach ($cards_projects as $project)
                        <x-project-card :$project :showTags="false" />
                    @endforeach
                    @if ($cards_article)
                        <x-blog-article-card :article="$cards_article" :showTags="false" :showDate="false"
                            imageHeight="h-56 lg:h-64" />
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ============================== CTA FINALE ============================== --}}
    <section data-reveal class="border-t border-light/15 py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <x-eyebrow>{{ __("Don't wait") }}</x-eyebrow>

            <h2
                class="mt-6 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[16ch]">
                {{ __('Not sure where to start?') }}
            </h2>

            <p class="mt-5 font-black uppercase leading-none tracking-tighter text-light/55 text-3xl lg:text-4xl">
                {{ __("Let's talk about it.") }}
            </p>

            <p class="mt-6 text-light/55 text-lg lg:text-xl font-light leading-relaxed max-w-[48ch]">
                {{ __('15 minutes to frame the problem and figure out where to act. Free, no strings attached.') }}
            </p>

            <div class="mt-11 flex flex-col sm:flex-row gap-4">
                <x-button :href="route('contacts')" data-pan="cta-services-contacts">{{ __('Book a call') }}</x-button>
                <x-button :href="route('projects')" variant="secondary"
                    data-pan="cta-services-projects">{{ __('See projects') }}</x-button>
            </div>
        </div>
    </section>
</div>
