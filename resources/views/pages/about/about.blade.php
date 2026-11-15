@php
    $timeline = [
        ['As a kid', 'Paint and the first question', 'A doodle becomes an image on a screen. How? I never stopped asking myself that.'],
        ['School', 'Computer science, no other way', 'There was no plan B. The computer science track was the only path that made sense.'],
        ['Web agency', 'Learning the trade in the field', 'Websites, deadlines, real clients. The best school to learn what the people who pay actually need.'],
        ['Business software', 'The step up', 'From websites to management systems: processes, data, people who work with them eight hours a day.'],
        ['Lead Developer', 'Leading a custom ERP', 'Architecture, technical choices, responsibility for the result. No longer just code.'],
        ['Today', 'Independent, by choice', 'I work with companies that entrust me with their projects, follow clients directly on commission and teach programming at a web academy. Only bespoke projects, with my own method.'],
    ];

    $hobbies = [
        ['Friends', 'The thing that gives me the most joy, which is why it comes before everything else. A pizza, a board game or an evening out dancing is enough: what matters is being myself and recharging with the right people.'],
        ['Motorbikes', 'My friends are to blame, and they did not have to insist much. In May 2025 my first bike arrived, a red and silver Kawasaki Z650. A passion that was just waiting to be discovered.'],
        ['Graphic design', 'It was born together with my passion for code. As a teenager I spent hours on photo manipulation in Photoshop and motion design in After Effects, and in high school I seriously considered it as a career. In the end software won, but I carry that eye into every interface I design.'],
        ['Ethical hacking', 'I am far from an expert level, and I say it without any problem. But the curiosity is real and comes from far away: reverse engineering fascinates me because it takes something apart to understand how it was built. Challenges on dedicated platforms taught me to think about security from another angle. If tech asked me for another path, this would be it.'],
        ['Video games', 'They have been with me forever: from a NES-like console with Super Mario to the PC of today. Among the ones I love are Ratchet & Clank, Spyro, Horizon, The Division, Rocket League and Trackmania. And the list would be much longer.'],
        ['Gym and running', 'More a conscious choice than a declared passion. With a job that is all head and chair, the physical counterweight is not optional: it is maintenance, just like for software.'],
    ];
@endphp

<div>
    <x-page-hero effect="pixel-paint" :label="__('Simone Cerruti, independent software developer. I am not an agency.')"
        :rows="[__('I am not'), __('an agency.')]">
        <p>{!! __('I am <b>Simone Cerruti</b>, an independent software developer. I build management systems, ERPs, CRMs, platforms and automations for companies that stopped <em>adapting</em> to other people\'s tools.') !!}</p>

        <x-slot:actions>
            <a class="button-primary button-large magnetic-hover home-hero-cta" href="{{ route('contacts') }}" wire:navigate data-cursor="{{ __('Write to me') }}"
                data-pan="cta-about-contacts"><span>{{ __('Let\'s talk') }}</span><span class="button-arrow">→</span></a>
        </x-slot:actions>
    </x-page-hero>

    <section class="section">
        <div class="about-intro">
            <div class="portrait-frame" data-effect="portrait-smudge" data-cursor="{{ __('Hi') }}">
                <img src="{{ asset('images/about_protrait.webp') }}" alt="{{ __('Portrait of Simone Cerruti') }}"
                    loading="lazy">
                <span class="before-after-screen-watermark" aria-hidden="true">SC</span>
            </div>
            <div>
                <h2 class="section-title reveal">{!! __('I do not<br><em>just build websites.</em>') !!}</h2>
                <p class="section-lead reveal" style="margin-top: 28px">{{ __('I develop complex, bespoke software: management systems, ERPs, CRMs, web platforms, automations and AI-based tools. Performance, reliability and scalability are not optional.') }}</p>
                <p class="section-lead reveal" style="margin-top: 18px">{{ __('I am not a supplier. I am the technical partner who learns how your business works before touching a line of code.') }}</p>
                <div class="chip-list reveal" style="margin-top: 28px">
                    @foreach (['ERP', 'CRM', __('Web platforms'), __('Automations'), 'PWA', 'AI'] as $tag)
                        <span>{{ $tag }}</span>
                    @endforeach
                </div>
                <div style="margin-top: 30px"><a class="button-outline-link" href="{{ route('how_i_work') }}" wire:navigate
                        data-pan="cta-about-method">{{ __('How I work') }} <span>→</span></a></div>
            </div>
        </div>
    </section>

    <section class="section" id="about-how-i-started" data-effect="about">
        <div class="about-paint">
            <div>
                <h2 class="section-title reveal">{!! __('It all started<br>with <span class="text-accent">Paint.</span>') !!}</h2>
                <p class="section-lead reveal" style="margin-top: 28px">{{ __('I was between 4 and 6 years old. My mom was ironing and let me draw in Paint, on an old computer with a CRT monitor and a trackball mouse. And I kept wondering: how does this box understand what I want to draw, and make it appear on the screen?') }}</p>
                <p class="section-lead reveal" style="margin-top: 18px">{{ __('That question never left. In high school I chose computer science without a second thought, and I never regretted it.') }}</p>
            </div>
            <div class="about-paint-screen reveal">
                <div class="about-paint-screen-bar"><span>{{ __('Draw. Like I used to.') }}</span>
                    <div class="about-paint-screen-tools" role="group" aria-label="{{ __('Colors') }}">
                        <button type="button" class="is-active" data-col="#0A0A0A" style="background: #0A0A0A"
                            aria-label="{{ __('Black') }}"></button>
                        <button type="button" data-col="#FF4A1C" style="background: #FF4A1C"
                            aria-label="{{ __('Orange') }}"></button>
                        <button type="button" data-col="#2F6BFF" style="background: #2F6BFF"
                            aria-label="{{ __('Blue') }}"></button>
                        <button type="button" class="about-paint-screen-clear" id="paint-clear-button">{{ __('Clear') }}</button>
                    </div>
                </div>
                <canvas id="pixel-paint-canvas" width="320" height="240" aria-label="{{ __('Paint-style drawing area') }}"
                    data-cursor="{{ __('Draw') }}" data-hello="{{ __('hello') }}"></canvas>
            </div>
        </div>
    </section>

    <section class="section section--tight">
        <div class="section-header"><h2 class="section-title reveal">{!! __('The road<span class="text-accent">.</span>') !!}</h2>
            <p class="section-lead section-lead--muted reveal">{{ __('From the first question to today.') }}</p>
        </div>
        <div class="about-timeline">
            @foreach ($timeline as [$when, $title, $text])
                <div class="about-timeline-item reveal"><span class="about-timeline-date">{{ __($when) }}</span>
                    <div>
                        <h3>{{ __($title) }}</h3>
                        <p>{{ __($text) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="section-header"><h2 class="section-title reveal">{!! __('Independent.<br><em>Not isolated.</em>') !!}</h2>
            <p class="section-lead reveal">{{ __('Working on my own is a choice, not a fallback. There are already people who entrust me with their work, every day.') }}</p>
        </div>
        <div class="about-trust">
            <div class="about-trust-item reveal">
                <h3>{{ __('They entrust me with their projects.') }}</h3>
                <p>{{ __('I work with companies that put the development of their software in my hands. Not for a lower quote, but for how I work.') }}</p>
            </div>
            <div class="about-trust-item reveal">
                <h3>{{ __('Commissions without middlemen.') }}</h3>
                <p>{{ __('Clients who choose me directly and with whom I work side by side, from the first call to after delivery.') }}</p>
            </div>
            <div class="about-trust-item reveal about-trust-item--highlight">
                <h3>{{ __('I teach those who will do my job.') }}</h3>
                <p>{{ __('I am a teacher at a web academy for programmers. They valued me enough to entrust me with the training of those who will work in software tomorrow.') }}</p>
            </div>
        </div>
    </section>

    <section class="section scroll-lit">
        <p data-accent-pattern="{{ __('passion|way') }}">{{ __('I do it out of passion. Code is my job and my hobby. I chose to work on my own to do it my way, with real attention to every project.') }}</p>
        <div class="section-lead reveal" style="margin-top: 40px">{{ __('The more skills I add, the more my passion grows: new technologies energize me, they do not tire me. And I learned one thing: quality cannot be improvised in contexts that do not allow it. That is why I built the context myself.') }}</div>
    </section>

    <section class="section section--tight">
        <div class="section-header"><h2 class="section-title reveal">{!! __('I never stop<br>learning<span class="text-accent">.</span>') !!}</h2>
            <p class="section-lead reveal">{{ __('In software, evolution is exponential: keeping up is part of the craft, and it is the part I enjoy most. AI is the biggest change of these years and ignoring it would be dishonest. For me it became a strength: I use it as a tool, not a shortcut, I evaluate every result and the control stays mine.') }}</p>
        </div>
        <div class="tech-cloud" id="tech-cloud-canvas" data-effect="tech-cloud">
            @foreach (['Laravel', 'Livewire', 'Filament', 'PHP', 'Vue', 'TypeScript', 'Tailwind', 'Docker', 'AI'] as $tech)
                <span>{{ $tech }}</span>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="section-header"><h2 class="section-title reveal">{!! __('Outside<br>the code<span class="text-accent">.</span>') !!}</h2>
            <p class="section-lead section-lead--muted reveal">{{ __('What I am when I close the laptop. Hover over them, or tap on a phone.') }}</p>
        </div>
        <div class="accordion">
            @foreach ($hobbies as [$name, $text])
                <button class="accordion-item" type="button">
                    <div class="accordion-item-head"><span>{{ __($name) }}</span><em>+</em></div>
                    <div class="accordion-item-body">
                        <div>
                            <p>{{ __($text) }}</p>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    </section>

    <x-contact-band />
</div>
