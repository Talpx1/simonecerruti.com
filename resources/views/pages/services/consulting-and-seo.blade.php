@php
    // Highlight markup for headings — injects the <x-highlighted-text> component
    // tag, compiled at runtime by Blade::render(). MUST stay in a @php block: a
    // literal <x-...> tag inside {!! !!} is rewritten at compile time and breaks.
    // See resources/views/pages/services/services.blade.php for the full rationale.
    $hl_tags = ['tag' => '<x-highlighted-text>', 'close_tag' => '</x-highlighted-text>'];
@endphp

<div>
    {{-- ============================== HERO ============================== --}}
    <section data-pan="section-impression-services-consulting-and-seo"
        class="relative overflow-hidden pt-12 lg:pt-16 pb-16 lg:pb-24">
        <div class="relative max-w-7xl mx-auto px-8 lg:px-14">

            <x-app-logo weight="thin"
                class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 w-[min(60vw,720px)] opacity-5 hidden lg:block" />

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-[1.15fr_0.85fr] gap-10 lg:gap-16 items-center">
                <div>
                    <x-eyebrow>{{ __('Area 03 — Consulting · SEO') }}</x-eyebrow>

                    <h1
                        class="mt-6 font-black uppercase leading-none tracking-tighter text-light text-5xl sm:text-6xl lg:text-7xl xl:text-8xl max-w-[14ch]">
                        {!! Blade::render(__('First the<br>:tag strategy :close_tag, then the code', $hl_tags)) !!}
                    </h1>

                    <p class="mt-7 text-light/55 text-lg lg:text-xl font-light leading-relaxed max-w-2xl">
                        {{ __('Let\'s figure out together what you really need before building anything: goals, priorities and technology choices made in your interest. And a website that is born already polished to be found on Google.') }}
                    </p>

                    <div class="mt-9 flex flex-wrap items-center gap-x-6 gap-y-4">
                        <x-button :href="route('contacts')" data-pan="cta-services-consulting-and-seo-consultation">{{ __('Request a consultation') }}</x-button>
                        @if ($case_project)
                            <x-pages::services.components.contextual-link
                                :href="route('project.show', $case_project->slug)" :prefix="__('Project:')"
                                :label="$case_project->title" pan="cta-services-consulting-and-seo-project" />
                        @endif
                    </div>

                    <div class="mt-9 flex flex-wrap gap-2.5">
                        @foreach ([__('Strategic consulting'), __('SEO & positioning'), __('Support for your team')] as $pill)
                            <span
                                class="inline-flex items-center gap-2.5 text-xs uppercase tracking-[0.1em] text-light/55 border border-light/15 px-3.5 py-2.5">
                                <span class="w-1.5 h-1.5 shrink-0 bg-light" aria-hidden="true"></span>
                                {{ $pill }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div>
                    <x-pages::services.components.mock-seo />
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== È PER TE SE ============================== --}}
    <section class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14 grid grid-cols-1 lg:grid-cols-[0.8fr_1.2fr] gap-10 lg:gap-16 items-start">
            <div>
                <x-eyebrow>{{ __('This is for you if…') }}</x-eyebrow>
                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                    {!! __('Do you<br>recognise at<br>least one?') !!}
                </h2>
                <p class="mt-6 text-light/55 text-base lg:text-lg font-light leading-relaxed max-w-[34ch]">
                    {{ __('If even one of these rings a bell, starting from strategy lets you spend better and reach the right result sooner.') }}
                </p>
            </div>

            @php
                $pains = [
                    [__('You have an idea but don\'t know where to start.'), __('You know what you\'d like to achieve, but not which tool you really need, nor which priorities to move on first.')],
                    [__('You\'ve already invested in digital with no results.'), __('A website or software that didn\'t bring what you hoped: before redoing everything, it\'s worth understanding why.')],
                    [__('Nobody finds you on Google.'), __('Whoever searches for your services ends up with your competitors, and you are left paying for every single lead through advertising.')],
                    [__('You have quotes on the table and can\'t tell them apart.'), __('Different proposals, difficult words and figures far apart: you need an independent opinion, on your side.')],
                    [__('You have an in-house team that needs guidance.'), __('The skills are there, but nobody is holding the course and bringing method to everyone\'s work.')],
                ];
            @endphp

            <div class="flex flex-col">
                @foreach ($pains as $i => [$pain_lead, $pain_body])
                    <div class="flex gap-4 lg:gap-5 items-start py-6 border-t border-light/15 @if ($loop->last) border-b @endif">
                        <span class="text-xs text-light/40 pt-1 shrink-0 w-8">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <p class="text-lg lg:text-xl leading-snug">
                            <b class="font-bold text-light">{{ $pain_lead }}</b>
                            <span class="text-light/55"> {{ $pain_body }}</span>
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== SOTTO-SERVIZI ============================== --}}
    <section class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <div class="mb-12 lg:mb-16">
                <x-eyebrow>{{ __('Three answers, one direction') }}</x-eyebrow>
                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[20ch]">
                    {!! __('What I offer<br>in this area') !!}
                </h2>
            </div>

            @php
                $sub_services = [
                    [
                        __('Strategic consulting'),
                        __('We bring goals and priorities into focus before building: we work out what you really need and map the road to get there, with no waste.'),
                        [
                            __('Goals and priorities put down in black and white'),
                            __('Technology choices made in your interest'),
                            __('A clear roadmap, step by step'),
                            __('Other people\'s quotes read with an independent eye'),
                        ],
                    ],
                    [
                        __('SEO & positioning'),
                        __('A site that gets found by whoever is looking for what you offer: solid technical foundations, the right content and a path to climb Google over time.'),
                        [
                            __('Keyword and competitor analysis'),
                            __('Solid technical foundations from day one'),
                            __('Content designed for whoever searches for your services'),
                            __('Results measured with clear data'),
                        ],
                    ],
                    [
                        __('Support & audit'),
                        __('A clear-eyed review of what you already have and support for your team when needed: I bring method and experience, without taking anyone\'s place.'),
                        [
                            __('A review of your existing website and software'),
                            __('Technical guidance for your in-house team'),
                            __('A clear, shared way of working'),
                            __('A point of reference, when needed'),
                        ],
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 border-t border-l border-light/15">
                @foreach ($sub_services as $i => [$sub_title, $sub_desc, $sub_bullets])
                    <article class="p-7 lg:p-8 border-r border-b border-light/15 flex flex-col">
                        <p class="text-xs tracking-[0.18em] text-light/40 mb-6">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</p>
                        <h3 class="font-black uppercase tracking-tight leading-none text-light text-2xl lg:text-3xl mb-4">{{ $sub_title }}</h3>
                        <p class="text-light/55 text-sm leading-relaxed font-light mb-6">{{ $sub_desc }}</p>
                        <ul class="flex flex-col gap-2.5">
                            @foreach ($sub_bullets as $sub_bullet)
                                <li class="flex gap-3 text-sm leading-snug">
                                    <x-ri-arrow-right-long-line class="w-3.5 mt-1 shrink-0 text-light" />
                                    <span class="text-light/55">{{ $sub_bullet }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== PROCESSO ============================== --}}
    <section class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <div class="mb-12 lg:mb-16">
                <x-eyebrow>{{ __('How I work') }}</x-eyebrow>
                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[22ch]">
                    {!! __('From the problem<br>to the right path, in 5 steps') !!}
                </h2>
            </div>

            @php
                $steps = [
                    [__('Listening'), __('You tell me where you are and where you want to go: goals, constraints and what you have already tried.')],
                    [__('Analysis'), __('I study your market, your competitors and how people find you online today.')],
                    [__('Strategy'), __('Together we set priorities and direction: what to do, what not to, and why.')],
                    [__('Roadmap'), __('I hand you a clear, ordered plan, with the right steps and the right investment.')],
                    [__('Support'), __('I walk the path with you, on my own or alongside your team, all the way to the result.')],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 border-t border-l border-light/15">
                @foreach ($steps as $i => [$step_title, $step_body])
                    <div class="p-7 lg:p-7 border-r border-b border-light/15">
                        <div class="flex items-center gap-2 text-xs text-light/40 mb-7">
                            {{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}
                            <span class="h-px flex-1 bg-light/15"></span>
                        </div>
                        <h3 class="font-black uppercase tracking-tight leading-tight text-light text-lg lg:text-xl mb-3">{{ $step_title }}</h3>
                        <p class="text-light/55 text-sm leading-relaxed font-light">{{ $step_body }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== VALORI ============================== --}}
    <section class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <x-eyebrow>{{ __('Why start from strategy') }}</x-eyebrow>
            <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[18ch]">
                {{ __('The advantages of deciding before building') }}
            </h2>

            @php
                $values = [
                    [__('Spend better'), __('Working out what you really need first saves you from paying for useless features or redoing the work from scratch soon after.')],
                    [__('Independent opinion'), __('No off-the-shelf solution to push: I recommend what is worth it for you, even when the answer is "not now".')],
                    [__('Clear direction'), __('An ordered roadmap that says what to do, in what order and why: you decide with a clear head, instead of sailing blind.')],
                    [__('Found on Google'), __('Foundations looked after from the start: your site is born already ready to be found by whoever searches for what you offer.')],
                ];
            @endphp

            <div class="mt-12 lg:mt-14 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 border-t border-light/15">
                @foreach ($values as $i => [$value_title, $value_body])
                    <div @class([
                        'p-7 lg:p-8 border-b border-light/15',
                        'md:border-l' => in_array($i, [1, 3], true),
                        'lg:border-l' => $i === 2,
                    ])>
                        <div class="text-xs text-light/40 mb-5">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</div>
                        <h3 class="font-black uppercase tracking-tight text-light text-xl lg:text-2xl mb-3">{{ $value_title }}</h3>
                        <p class="text-light/55 text-sm leading-relaxed font-light">{{ $value_body }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== STRUMENTI & METODO ============================== --}}
    <section class="border-t border-light/15 py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-8 lg:px-14 grid grid-cols-1 lg:grid-cols-[0.7fr_1.3fr] gap-10 lg:gap-16 items-center">
            <div>
                <x-eyebrow>{{ __('Tools & method') }}</x-eyebrow>
                <p class="mt-5 text-light/55 text-base lg:text-lg font-light leading-relaxed max-w-[32ch]">
                    {{ __('An orderly way of working and solid tools to read the data — so choices come from facts, not impressions.') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                @foreach ([__('SEO audit'), __('Keyword research'), __('Competitor analysis'), __('Content architecture'), __('Reading the data'), __('Speed & mobile'), __('Prioritised roadmap'), __('Clear reports')] as $tool)
                    <span class="text-sm text-light border border-light/15 px-5 py-3 hover:bg-light hover:text-dark transition-colors duration-200">{{ $tool }}</span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== AI BAND ============================== --}}
    <x-pages::services.components.ai-band :eyebrow="__('With AI')"
        :lead="__('I use artificial intelligence to read large amounts of data and content and surface what matters — always with a critical eye, and the final call yours.')"
        :points="[
            [__('Deeper analysis.'), __('I surface opportunities and patterns from data, content and research that would take an enormous amount of work by hand.')],
            [__('Better-informed decisions.'), __('AI suggests, I weigh in: every recommendation stays carefully vetted, for the utmost quality of what I hand over.')],
        ]">
        {!! __('AI in service<br>of strategy') !!}
    </x-pages::services.components.ai-band>

    {{-- ============================== CASO REALE ============================== --}}
    @if ($case_project)
        <section id="case" class="border-t border-light/15 py-20 lg:py-28 scroll-mt-24">
            <div class="max-w-7xl mx-auto px-8 lg:px-14">
                <x-pages::services.components.feature-row :area="__('Real case — Strategy & SEO')"
                    :lead="__('Before rebuilding the site, we worked out who was searching for those services and with which words. The new project was born around those answers — and leads from the web started arriving on their own.')"
                    :bullets="[
                        __('Strategy and content decided before writing a line of code.'),
                        __('Pages built around what people actually search for.'),
                        __('More leads from the site, less spent on advertising.'),
                    ]"
                    :button-label="__('Read the case study')"
                    :button-href="route('project.show', $case_project->slug)" button-pan="cta-services-consulting-and-seo-case">
                    <x-slot:media>
                        <x-pages::services.components.mock-seo />
                    </x-slot:media>
                    <x-slot:heading>{{ $case_project->title }}</x-slot:heading>
                    <x-slot:contextual>
                        <a wire:navigate href="{{ route('projects') }}"
                            class="group/cta inline-flex items-center gap-2 text-xs uppercase tracking-[0.08em] text-light/55 hover:text-light transition-colors">
                            {{ __('All projects') }}
                            <x-ri-arrow-right-long-line class="w-3.5 shrink-0 transition-transform group-hover/cta:translate-x-1" />
                        </a>
                    </x-slot:contextual>
                </x-pages::services.components.feature-row>
            </div>
        </section>
    @endif

    {{-- ============================== FAQ ============================== --}}
    <section class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14 grid grid-cols-1 lg:grid-cols-[0.7fr_1.3fr] gap-10 lg:gap-16 items-start">
            <div>
                <x-eyebrow>{{ __('Frequently asked questions') }}</x-eyebrow>
                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                    {!! __('The answers<br>you\'re after') !!}
                </h2>
                <p class="mt-6 text-light/55 text-base lg:text-lg font-light leading-relaxed max-w-[30ch]">
                    {{ __('Questions about how a consultation works, the timing or SEO results? Here are the ones I get asked most often.') }}
                </p>
                <a href="{{ route('contacts') }}" wire:navigate
                    class="group/cta mt-7 inline-flex items-center gap-2 text-xs uppercase tracking-[0.08em] text-light/55 hover:text-light transition-colors">
                    {{ __('Got another question? Write to me') }}
                    <x-ri-arrow-right-long-line class="w-3.5 shrink-0 transition-transform group-hover/cta:translate-x-1" />
                </a>
            </div>

            @php
                $faqs = [
                    [__('How does a consultation work?'), __('We start with a free call where you tell me where you are and where you want to go. If it makes sense to carry on, I analyse your situation and hand you a clear strategy and roadmap: what to do, in what order and why. You stay free to carry it out with me, with your team or with whoever you prefer.')],
                    [__('Do I have to carry out the project with you?'), __('No. The consultation has value on its own: it leaves you a clear, independent direction. If you then want me to take care of it, great; otherwise the plan stays yours and you can take it to anyone.')],
                    [__('How long before SEO results show?'), __('SEO is ongoing work: the first technical foundations are looked after straight away, while positioning grows over the following weeks and months. I always tell you honestly what to expect and in what timeframe, with no easy promises of a "guaranteed first page".')],
                    [__('Can you help me choose between different quotes and vendors?'), __('Yes. I read the proposals you have on the table and translate them into plain words: what you are really buying, what is missing and where the risks are. An opinion on your side, because I have no product to sell you at all costs.')],
                    [__('How much does a consultation cost?'), __('It depends on what you need: a focused opinion, a full strategy or ongoing support over time. The first call is free and with no obligation; only after understanding your case do I propose something concrete and tailor-made.')],
                    [__('Can you support my in-house team?'), __('Of course. I can work alongside the people you already have, bringing method, experience and technical guidance where it is needed, without replacing anyone. We decide together how much and how to be present.')],
                    [__('Do you offer consulting even if I don\'t have a website yet?'), __('Yes — in fact it is the best moment. Starting from strategy before building saves you time and money: we decide together what you really need, so the site or software is born heading in the right direction.')],
                ];
            @endphp

            <div class="border-t border-light/15">
                @foreach ($faqs as [$question, $answer])
                    <details class="group/faq border-b border-light/15">
                        <summary class="flex items-start justify-between gap-6 py-6 cursor-pointer list-none font-bold text-lg lg:text-2xl leading-tight tracking-tight text-light/90 hover:text-light transition-colors">
                            {{ $question }}
                            <span class="relative w-5 h-5 mt-1 shrink-0" aria-hidden="true">
                                <span class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-0.5 bg-light"></span>
                                <span class="absolute inset-y-0 left-1/2 -translate-x-1/2 w-0.5 bg-light transition-opacity group-open/faq:opacity-0"></span>
                            </span>
                        </summary>
                        <p class="text-light/55 text-base leading-relaxed font-light max-w-[70ch] pb-7 pr-0 lg:pr-12">{{ $answer }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== CTA FINALE ============================== --}}
    <section class="border-t border-light/15 py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <x-eyebrow>{{ __("Don't wait") }}</x-eyebrow>

            <h2 class="mt-6 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[16ch]">
                {!! __('Let\'s figure out together<br>where to start?') !!}
            </h2>

            <p class="mt-7 font-black uppercase leading-none tracking-tighter text-light/55 text-3xl lg:text-4xl">
                {{ __("Let's talk about it.") }}
            </p>

            <p class="mt-6 text-light/55 text-lg lg:text-xl font-light leading-relaxed max-w-[48ch]">
                {{ __('15 minutes to frame the problem and figure out where to act. Free, no strings attached.') }}
            </p>

            <div class="mt-11 flex flex-col sm:flex-row gap-4">
                <x-button :href="route('contacts')" data-pan="cta-services-consulting-and-seo-contacts">{{ __('Book a call') }}</x-button>
                <x-button :href="route('services')" variant="secondary" data-pan="cta-services-consulting-and-seo-back">{{ __('Back to services') }}</x-button>
            </div>
        </div>
    </section>
</div>
