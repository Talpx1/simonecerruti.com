@php
    // Highlight markup for headings — injects the <x-highlighted-text> component
    // tag, compiled at runtime by Blade::render(). MUST stay in a @php block: a
    // literal <x-...> tag inside {!! !!} is rewritten at compile time and breaks.
    // See resources/views/pages/services/services.blade.php for the full rationale.
    $hl_tags = ['tag' => '<x-highlighted-text>', 'close_tag' => '</x-highlighted-text>'];
@endphp

<div>
    {{-- ============================== HERO ============================== --}}
    <section data-pan="section-impression-services-custom-software-development"
        class="relative overflow-hidden pt-12 lg:pt-16 pb-16 lg:pb-24">
        <div class="relative max-w-7xl mx-auto px-8 lg:px-14">

            <x-app-logo weight="thin"
                class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 w-[min(60vw,720px)] opacity-5 hidden lg:block" />

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-[1.15fr_0.85fr] gap-10 lg:gap-16 items-center">
                <div>
                    <x-eyebrow>{{ __('Area 01 — Management software · ERP · CRM') }}</x-eyebrow>

                    <h1
                        class="mt-6 font-black uppercase leading-none tracking-tighter text-light text-5xl sm:text-6xl lg:text-7xl xl:text-8xl max-w-[14ch]">
                        {!! Blade::render(__('Software that makes<br>the company :tag run :close_tag', $hl_tags)) !!}
                    </h1>

                    <p class="mt-7 text-light/55 text-lg lg:text-xl font-light leading-relaxed max-w-2xl">
                        {{ __('Tailor-made management software, ERP and CRM for your SME: a single system stitched onto how you actually work. Orders, inventory, customers and invoicing in one place — no more scattered spreadsheets or double data entry.') }}
                    </p>

                    <div class="mt-9 flex flex-wrap items-center gap-x-6 gap-y-4">
                        <x-button :href="route('contacts')" data-pan="cta-services-custom-software-development-consultation">{{ __('Book a consultation') }}</x-button>
                        @if ($case_project)
                            <x-pages::services.components.contextual-link
                                :href="route('project.show', $case_project->slug)" :prefix="__('Project:')"
                                :label="$case_project->title" pan="cta-services-custom-software-development-project" />
                        @endif
                    </div>

                    <div class="mt-9 flex flex-wrap gap-2.5">
                        @foreach ([__('Tailor-made management software'), __('ERP systems'), __('CRM & customer management')] as $pill)
                            <span
                                class="inline-flex items-center gap-2.5 text-xs uppercase tracking-[0.1em] text-light/55 border border-light/15 px-3.5 py-2.5">
                                <span class="w-1.5 h-1.5 shrink-0 bg-light" aria-hidden="true"></span>
                                {{ $pill }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div>
                    <x-pages::services.components.mock-erp />
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== È PER TE SE ============================== --}}
    <section data-reveal class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14 grid grid-cols-1 lg:grid-cols-[0.8fr_1.2fr] gap-10 lg:gap-16 items-start">
            <div>
                <x-eyebrow>{{ __('This is for you if…') }}</x-eyebrow>
                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                    {!! __('Do you<br>recognise at<br>least one?') !!}
                </h2>
                <p class="mt-6 text-light/55 text-base lg:text-lg font-light leading-relaxed max-w-[34ch]">
                    {{ __('If even one of these rings a bell, a tailor-made system changes the way you work every single day.') }}
                </p>
            </div>

            @php
                $pains = [
                    [__('You live inside scattered spreadsheets.'), __('Duplicated data across many files, versions that don\'t match, and one person who "knows where everything is".')],
                    [__('Departments that don\'t talk to each other.'), __('Sales, warehouse and admin each work on their own: the same figure gets re-entered three times.')],
                    [__('Too many manual operations.'), __('Copy-paste, email confirmations, manual checks: hours lost and a high margin for error.')],
                    [__('Zero real-time visibility.'), __('To know how the business is doing you have to wait for someone to "prepare the file". Decisions arrive late.')],
                    [__('The off-the-shelf software feels too tight.'), __('You pay fees for features you don\'t use and adapt to processes that aren\'t yours, instead of the other way around.')],
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
    <section data-reveal class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <div class="mb-12 lg:mb-16">
                <x-eyebrow>{{ __('Three answers, one direction') }}</x-eyebrow>
                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[20ch]">
                    {!! __('What I build<br>in this area') !!}
                </h2>
            </div>

            @php
                $sub_services = [
                    [
                        __('Tailor-made management software'),
                        __('A single software stitched onto your flow: orders, inventory, production, documents and invoicing, with no useless modules.'),
                        [
                            __('Records, orders and inventory connected'),
                            __('Automation of repetitive flows'),
                            __('Roles and permissions for each team'),
                            __('Reports and KPIs always up to date'),
                        ],
                    ],
                    [
                        __('ERP systems'),
                        __('Every department on a single database: sales, purchasing, inventory and accounting speak the same language, in real time.'),
                        [
                            __('All processes connected, from start to finish'),
                            __('One figure, valid for every department'),
                            __('Integrations with invoicing & e-commerce'),
                            __('Grows module by module, when needed'),
                        ],
                    ],
                    [
                        __('CRM & customers'),
                        __('Records, deals and contacts always under control: you know where every customer stands and no opportunity slips away.'),
                        [
                            __('The status of every deal, always clear'),
                            __('Contact and activity history'),
                            __('Automatic reminders and follow-ups'),
                            __('Connected to orders and invoicing'),
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
    <section data-reveal class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <div class="mb-12 lg:mb-16">
                <x-eyebrow>{{ __('How I work') }}</x-eyebrow>
                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[22ch]">
                    {!! __('From your process<br>to the software, in 5 steps') !!}
                </h2>
            </div>

            @php
                $steps = [
                    [__('Analysis'), __('I study how you really work: flows, people, tools and bottlenecks.')],
                    [__('Design'), __('We shape the tailor-made system and the priorities together, module by module.')],
                    [__('Development'), __('I build in short cycles: you see progress and give feedback along the way.')],
                    [__('Release'), __('Data migration, going live and training the team on everyday use.')],
                    [__('Evolution'), __('Ongoing support: the software grows with the company, one feature at a time.')],
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
    <section data-reveal class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <x-eyebrow>{{ __('Why bespoke') }}</x-eyebrow>
            <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[18ch]">
                {{ __('The advantages of management software tailored to you') }}
            </h2>

            @php
                // The first three reuse the Services hub's exact strings (shared i18n keys).
                $values = [
                    [__('Full flexibility'), __('You don\'t adapt to the software: it shapes itself around your needs and grows with your company, one feature at a time.')],
                    [__('100% your code'), __('You own the code. No lifelong fees, no lock-in: if you change provider, the software stays yours.')],
                    [__('Full control'), __('Features, scalability, look and functionality: you decide, in direct contact with whoever builds the product.')],
                    [__('Real-time data'), __('A single up-to-date figure for everyone, always available: faster, better-informed decisions, without waiting for the "right file".')],
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

    {{-- ============================== STACK ============================== --}}
    <section data-reveal class="border-t border-light/15 py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-8 lg:px-14 grid grid-cols-1 lg:grid-cols-[0.7fr_1.3fr] gap-10 lg:gap-16 items-center">
            <div>
                <x-eyebrow>{{ __('Stack & technologies') }}</x-eyebrow>
                <p class="mt-5 text-light/55 text-base lg:text-lg font-light leading-relaxed max-w-[32ch]">
                    {{ __('Solid, standard technologies, chosen to fit the problem — never for hype, nor for constraints that would tie your hands.') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                @foreach (['Laravel', 'PHP', 'PostgreSQL', 'Vue.js', __('REST API'), __('Webhooks & integrations'), 'Docker', 'Git'] as $tech)
                    <span class="text-sm text-light border border-light/15 px-5 py-3 hover:bg-light hover:text-dark transition-colors duration-200">{{ $tech }}</span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== AI BAND ============================== --}}
    <x-pages::services.components.ai-band :eyebrow="__('With AI')"
        :lead="__('I build artificial intelligence into the software I create — to automate what eats up your time today and surface what matters from your data.')"
        :points="[
            [__('AI built into your processes.'), __('Internal assistants, smart automations, semantic search and data analysis, inside your management software or CRM.')],
            [__('Development with next-generation tools.'), __('I build more ambitious, more polished systems, without ballooning cost and complexity.')],
        ]">
        {!! __('AI inside your<br>management software') !!}
    </x-pages::services.components.ai-band>

    {{-- ============================== CASO REALE ============================== --}}
    @if ($case_project)
        <section id="case" data-reveal class="border-t border-light/15 py-20 lg:py-28 scroll-mt-24">
            <div class="max-w-7xl mx-auto px-8 lg:px-14">
                <x-pages::services.components.feature-row flip :area="__('Real case — Management software & CRM')"
                    :lead="__('Tailor-made management software and CRM: records, projects and customers gathered into a single system, connected to the website.')"
                    :bullets="[
                        __('All the company data gathered in one place.'),
                        __('No more manual hand-offs between those who sell and those who deliver.'),
                        __('CRM connected to the website.'),
                    ]"
                    :button-label="__('Read the case study')"
                    :button-href="route('project.show', $case_project->slug)" button-pan="cta-services-custom-software-development-case">
                    <x-slot:media>
                        <x-pages::services.components.mock-dashboard />
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
    <section data-reveal class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14 grid grid-cols-1 lg:grid-cols-[0.7fr_1.3fr] gap-10 lg:gap-16 items-start">
            <div>
                <x-eyebrow>{{ __('Frequently asked questions') }}</x-eyebrow>
                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                    {!! __('The answers<br>you\'re after') !!}
                </h2>
                <p class="mt-6 text-light/55 text-base lg:text-lg font-light leading-relaxed max-w-[30ch]">
                    {{ __('Questions about costs, timing or technology? Here are the ones I get asked most often.') }}
                </p>
                <a href="{{ route('contacts') }}" wire:navigate
                    class="group/cta mt-7 inline-flex items-center gap-2 text-xs uppercase tracking-[0.08em] text-light/55 hover:text-light transition-colors">
                    {{ __('Got another question? Write to me') }}
                    <x-ri-arrow-right-long-line class="w-3.5 shrink-0 transition-transform group-hover/cta:translate-x-1" />
                </a>
            </div>

            @php
                $faqs = [
                    [__('How much does tailor-made management software cost?'), __('It depends on the processes to cover and the number of modules. That\'s why I don\'t sell off-the-shelf "packages": we start from a free call, define the priorities, and only then do I give you a concrete estimate. You can start from one essential module and expand over time, spreading the investment.')],
                    [__('How long does it take to develop?'), __('I work in short cycles: a first useful version is usually online within a few weeks, then it grows through iterations. You see progress along the way and give feedback, instead of waiting months for the final "big bang".')],
                    [__('Can I start with one module and add more later?'), __('Yes, it\'s the approach I recommend. We start from the process that wastes you the most time, put it into the system, and add modules one feature at a time as the company grows.')],
                    [__('Does it integrate with invoicing, e-commerce and the tools I already use?'), __('Yes. The management software connects to electronic-invoicing tools, e-commerce platforms and the other tools you already use, exchanging data automatically: so you avoid double data entry.')],
                    [__('Who owns the software code?'), __('It\'s 100% yours. No lifelong fees and no constraints tying you to me: if one day you want to change provider or bring development in-house, the code stays yours and documented.')],
                    [__('What\'s the difference between management software, ERP and CRM?'), __('Management software digitises everyday operations (orders, inventory, documents). ERP connects every department on a single database. CRM manages customers and deals. They often live together in the same tailor-made system: we start from what you actually need.')],
                    [__('Can I migrate data from my spreadsheets or old software?'), __('Yes. Data migration is part of the project: we import records, history and archives from spreadsheets or the previous software, with checks to avoid duplicates and errors.')],
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
    <section data-reveal class="border-t border-light/15 py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <x-eyebrow>{{ __("Don't wait") }}</x-eyebrow>

            <h2 class="mt-6 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[16ch]">
                {!! __('Shall we bring order<br>to your processes?') !!}
            </h2>

            <p class="mt-7 font-black uppercase leading-none tracking-tighter text-light/55 text-3xl lg:text-4xl">
                {{ __("Let's talk about it.") }}
            </p>

            <p class="mt-6 text-light/55 text-lg lg:text-xl font-light leading-relaxed max-w-[48ch]">
                {{ __('15 minutes to see where you lose time and which tailor-made system is really worth it for you. Free, no strings attached.') }}
            </p>

            <div class="mt-11 flex flex-col sm:flex-row gap-4">
                <x-button :href="route('contacts')" data-pan="cta-services-custom-software-development-contacts">{{ __('Book a call') }}</x-button>
                <x-button :href="route('services')" variant="secondary" data-pan="cta-services-custom-software-development-back">{{ __('Back to services') }}</x-button>
            </div>
        </div>
    </section>
</div>
