@php
    // Highlight markup for headings — injects the <x-highlighted-text> component
    // tag, compiled at runtime by Blade::render(). MUST stay in a @php block: a
    // literal <x-...> tag inside {!! !!} is rewritten at compile time and breaks.
    // See resources/views/pages/services/services.blade.php for the full rationale.
    $hl_tags = ['tag' => '<x-highlighted-text>', 'close_tag' => '</x-highlighted-text>'];
@endphp

<div>
    {{-- ============================== HERO ============================== --}}
    <section data-pan="section-impression-services-web-development"
        class="relative overflow-hidden pt-12 lg:pt-16 pb-16 lg:pb-24">
        <div class="relative max-w-7xl mx-auto px-8 lg:px-14">

            <x-app-logo weight="thin"
                class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 w-[min(60vw,720px)] opacity-5 hidden lg:block" />

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-[1.15fr_0.85fr] gap-10 lg:gap-16 items-center">
                <div>
                    <x-eyebrow>{{ __('Area 02 — Websites · E-commerce · Platforms · Web apps') }}</x-eyebrow>

                    <h1
                        class="mt-6 font-black uppercase leading-none tracking-tighter text-light text-5xl sm:text-6xl lg:text-7xl xl:text-8xl max-w-[14ch]">
                        {!! Blade::render(__('The web that<br>:tag sells :close_tag and grows with you', $hl_tags)) !!}
                    </h1>

                    <p class="mt-7 text-light/55 text-lg lg:text-xl font-light leading-relaxed max-w-2xl">
                        {{ __('Tailor-made websites, e-commerce and platforms, built around the people you want to reach. Not just a site that looks good: a tool that brings in customers, sells and grows together with your business.') }}
                    </p>

                    <div class="mt-9 flex flex-wrap items-center gap-x-6 gap-y-4">
                        <x-button :href="route('contacts')" data-pan="cta-services-web-development-consultation">{{ __('Book a consultation') }}</x-button>
                        @if ($case_project)
                            <x-pages::services.components.contextual-link
                                :href="route('project.show', $case_project->slug)" :prefix="__('Project:')"
                                :label="$case_project->title" pan="cta-services-web-development-project" />
                        @endif
                    </div>

                    <div class="mt-9 flex flex-wrap gap-2.5">
                        @foreach ([__('Websites & landing'), __('E-commerce'), __('Platforms & web apps')] as $pill)
                            <span
                                class="inline-flex items-center gap-2.5 text-xs uppercase tracking-[0.1em] text-light/55 border border-light/15 px-3.5 py-2.5">
                                <span class="w-1.5 h-1.5 shrink-0 bg-light" aria-hidden="true"></span>
                                {{ $pill }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div>
                    <x-pages::services.components.mock-shop />
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== È PER TE SE ============================== --}}
    <x-pages::services.components.section>
        <div class="max-w-7xl mx-auto px-8 lg:px-14 grid grid-cols-1 lg:grid-cols-[0.8fr_1.2fr] gap-10 lg:gap-16 items-start">
            <div>
                <x-eyebrow>{{ __('This is for you if…') }}</x-eyebrow>
                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                    {!! __('Do you<br>recognise at<br>least one?') !!}
                </h2>
                <p class="mt-6 text-light/55 text-base lg:text-lg font-light leading-relaxed max-w-[34ch]">
                    {{ __('If even one of these rings a bell, a tailor-made web project changes the way you find and serve your customers.') }}
                </p>
            </div>

            @php
                $pains = [
                    [__('You have a website that brings in no customers.'), __('It has been online for years but generates no leads or sales: a still shop window, instead of a tool that works for you.')],
                    [__('Nobody finds you on Google.'), __('Competitors are on the first page and you are not: whoever searches for your services ends up with them.')],
                    [__('You still sell offline only.'), __('Orders by phone, WhatsApp or email: every sale takes up your time and, after hours, the shop is closed.')],
                    [__('The site is slow and looks bad on a phone.'), __('Pages that take forever to load and a frustrating mobile experience: people leave before buying.')],
                    [__('You have pieced together too many disconnected tools.'), __('Website, bookings, payments and management software live apart and you are the one bridging them, by hand.')],
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
    </x-pages::services.components.section>

    {{-- ============================== SOTTO-SERVIZI ============================== --}}
    <x-pages::services.components.section>
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
                        __('Websites & landing'),
                        __('A tailor-made site that tells who you are and turns visitors into customers: fast, polished and ready to be found on Google.'),
                        [
                            __('Tailor-made design, no off-the-shelf templates'),
                            __('Built to rank on Google'),
                            __('Lightning fast, even on a smartphone'),
                            __('Copy and structure designed to convert'),
                        ],
                    ],
                    [
                        __('E-commerce'),
                        __('An online shop that sells even when you are not there: catalogue, payments and shipping under control, connected to your management software.'),
                        [
                            __('Catalogue and orders always up to date'),
                            __('Payments and shipping integrated'),
                            __('Connected to inventory and invoicing'),
                            __('Built to bring customers back'),
                        ],
                    ],
                    [
                        __('Platforms & web apps'),
                        __('Tailor-made tools for your business: customer portals, reserved areas, bookings and management software reachable anywhere, straight from the browser.'),
                        [
                            __('Stitched onto your way of working'),
                            __('Reachable from every device'),
                            __('Reserved areas and roles for each user'),
                            __('They grow in features, when needed'),
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
    </x-pages::services.components.section>

    {{-- ============================== PROCESSO ============================== --}}
    <x-pages::services.components.section>
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <div class="mb-12 lg:mb-16">
                <x-eyebrow>{{ __('How I work') }}</x-eyebrow>
                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[22ch]">
                    {!! __('From your goal<br>to the website online, in 5 steps') !!}
                </h2>
            </div>

            @php
                $steps = [
                    [__('Analysis'), __('I understand goals, audience and competitors: what the site has to do and for whom.')],
                    [__('Design'), __('We shape structure, content and look together, page by page.')],
                    [__('Development'), __('I build in short cycles: you see the site take shape and give feedback along the way.')],
                    [__('Release'), __('Going live, connecting it to Google and checking that everything runs smoothly.')],
                    [__('Evolution'), __('Updates, new pages and features: the site grows with your business.')],
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
    </x-pages::services.components.section>

    {{-- ============================== VALORI ============================== --}}
    <x-pages::services.components.section>
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <x-eyebrow>{{ __('Why bespoke') }}</x-eyebrow>
            <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[18ch]">
                {{ __('The advantages of a website tailored to you') }}
            </h2>

            @php
                $values = [
                    [__('Full flexibility'), __('You don\'t fit into a template: the site is stitched onto your business and grows with you, one page and one feature at a time.')],
                    [__('Everything is yours'), __('Site, content and code are yours. No lifelong fees and no constraints tying you to the provider: if you move on, it stays yours.')],
                    [__('Full control'), __('Look, features and content: you decide, in direct contact with whoever builds the site, with no middlemen.')],
                    [__('Found on Google'), __('Solid foundations from day one: the site is born already ready to be found by whoever searches for what you offer.')],
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
    </x-pages::services.components.section>

    {{-- ============================== STACK ============================== --}}
    <x-pages::services.components.section padding="py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-8 lg:px-14 grid grid-cols-1 lg:grid-cols-[0.7fr_1.3fr] gap-10 lg:gap-16 items-center">
            <div>
                <x-eyebrow>{{ __('Stack & technologies') }}</x-eyebrow>
                <p class="mt-5 text-light/55 text-base lg:text-lg font-light leading-relaxed max-w-[32ch]">
                    {{ __('Solid, standard technologies, chosen to fit the problem — for a site that is fast, secure and ready to last over time.') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                @foreach (['Laravel', 'PHP', 'PostgreSQL', 'Vue.js', __('Technical SEO'), __('Payments & shipping'), 'Docker', 'Git'] as $tech)
                    <span class="text-sm text-light border border-light/15 px-5 py-3 hover:bg-light hover:text-dark transition-colors duration-200">{{ $tech }}</span>
                @endforeach
            </div>
        </div>
    </x-pages::services.components.section>

    {{-- ============================== AI BAND ============================== --}}
    <x-pages::services.components.ai-band :eyebrow="__('With AI')"
        :lead="__('I build artificial intelligence into the websites and platforms I create — to give your customers a richer, more useful and more polished experience.')"
        :points="[
            [__('AI built into your products.'), __('Smart search, personalised recommendations and assistants that answer your customers for you, inside the site or the e-commerce.')],
            [__('Development with next-generation tools.'), __('I deliver more ambitious, more polished projects, with the utmost attention to the quality of what I hand over.')],
        ]">
        {!! __('AI inside your<br>website') !!}
    </x-pages::services.components.ai-band>

    {{-- ============================== CASO REALE ============================== --}}
    @if ($case_project)
        <x-pages::services.components.section id="case" class="scroll-mt-24">
            <div class="max-w-7xl mx-auto px-8 lg:px-14">
                <x-pages::services.components.feature-row :area="__('Real case — Website & portal')"
                    :lead="__('Tailor-made website and portal: enrolments, paperwork and communications handled automatically, with no more queues at the desk.')"
                    :bullets="[
                        __('Enrolments and paperwork collected online, with no paper forms.'),
                        __('Students always up to date on deadlines and exams.'),
                        __('Website and portal connected into a single experience.'),
                    ]"
                    :button-label="__('Read the case study')"
                    :button-href="route('project.show', $case_project->slug)" button-pan="cta-services-web-development-case">
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
        </x-pages::services.components.section>
    @endif

    {{-- ============================== FAQ ============================== --}}
    <x-pages::services.components.section>
        <div class="max-w-7xl mx-auto px-8 lg:px-14 grid grid-cols-1 lg:grid-cols-[0.7fr_1.3fr] gap-10 lg:gap-16 items-start">
            <div>
                <x-eyebrow>{{ __('Frequently asked questions') }}</x-eyebrow>
                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                    {!! __('The answers<br>you\'re after') !!}
                </h2>
                <p class="mt-6 text-light/55 text-base lg:text-lg font-light leading-relaxed max-w-[30ch]">
                    {{ __('Questions about costs, timing or results? Here are the ones I get asked most often.') }}
                </p>
                <a href="{{ route('contacts') }}" wire:navigate
                    class="group/cta mt-7 inline-flex items-center gap-2 text-xs uppercase tracking-[0.08em] text-light/55 hover:text-light transition-colors">
                    {{ __('Got another question? Write to me') }}
                    <x-ri-arrow-right-long-line class="w-3.5 shrink-0 transition-transform group-hover/cta:translate-x-1" />
                </a>
            </div>

            @php
                $faqs = [
                    [__('How much does a tailor-made website or e-commerce cost?'), __('It depends on how many pages and features are needed and on what the site has to do. That\'s why I don\'t sell off-the-shelf "packages": we start from a free call, define the priorities, and only then do I give you a concrete estimate. You can start from the essentials and expand over time, spreading the investment.')],
                    [__('How long does it take to put it online?'), __('I work in short cycles: usually a first useful version is online within a few weeks, then it grows through iterations. You see the site take shape and give feedback along the way, without waiting months in the dark.')],
                    [__('Will the site be found on Google?'), __('Yes: the site is born with solid foundations to rank — structure, speed and content designed for whoever searches for your services. SEO is ongoing work, but you start off on the right foot instead of chasing later.')],
                    [__('Can I update the content myself?'), __('Yes. I hand over a site that you update on your own: text, images, products and pages are edited simply, without having to call a developer every time.')],
                    [__('Does it connect to my management software, payments and shipping?'), __('Yes. The site talks to the tools you already use — management software, online payments, couriers and invoicing — so orders arrive where they need to without manual double entry.')],
                    [__('Who owns the site once it\'s delivered?'), __('It\'s 100% yours. No lifelong fees and no constraints tying you to me: if one day you want to change provider, the site and its content stay yours.')],
                    [__('Will the site look good on a smartphone?'), __('Always. Every project is designed for the phone first: most of your customers visit you from there, so the mobile experience is as fast and polished as the desktop one.')],
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
    </x-pages::services.components.section>

    {{-- ============================== CTA FINALE ============================== --}}
    <x-pages::services.components.section padding="py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <x-eyebrow>{{ __("Don't wait") }}</x-eyebrow>

            <h2 class="mt-6 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[16ch]">
                {!! __('Shall we bring your<br>business online?') !!}
            </h2>

            <p class="mt-7 font-black uppercase leading-none tracking-tighter text-light/55 text-3xl lg:text-4xl">
                {{ __("Let's talk about it.") }}
            </p>

            <p class="mt-6 text-light/55 text-lg lg:text-xl font-light leading-relaxed max-w-[48ch]">
                {{ __('15 minutes to understand what your site or e-commerce really needs and how to make it pay off. Free, no strings attached.') }}
            </p>

            <div class="mt-11 flex flex-col sm:flex-row gap-4">
                <x-button :href="route('contacts')" data-pan="cta-services-web-development-contacts">{{ __('Book a call') }}</x-button>
                <x-button :href="route('services')" variant="secondary" data-pan="cta-services-web-development-back">{{ __('Back to services') }}</x-button>
            </div>
        </div>
    </x-pages::services.components.section>
</div>
