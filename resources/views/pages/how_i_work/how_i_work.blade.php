<div>
    <section class="flex flex-col px-8 lg:px-14 pb-20">
        <div class="flex gap-32">
            <h1 class="font-black uppercase leading-none tracking-tight text-7xl lg:text-8xl xl:text-9xl">
                {!! __('How <br> :tag I Work :tag_close', [
                    'tag' => '<span class="text-light/20">',
                    'tag_close' => '</span>',
                ]) !!}
            </h1>

            <x-app-logo class="max-w-96 hidden lg:block" />

        </div>

        <p class="mt-10 text-light/60 text-lg lg:text-xl font-light leading-relaxed max-w-2xl">
            {{ __("Software that doesn't break. Interfaces that don't frustrate. Results that outlast the trend. Here's what that actually means — and why it makes a difference.") }}
        </p>
    </section>

    <x-marquee :entries="[
        __('BESPOKE'),
        __('QUALITY'),
        trans_choice('RELIABLE', 1),
        __('SCALABLE'),
        __('NO LIMITS'),
        __('MODERN'),
        __('AUTOMATED'),
        __('TAILOR-MADE'),
    ]" />

    <section class="bg-dark px-8 lg:px-14 py-24 lg:py-36 border-b border-light/10">
        <p class="font-semibold leading-tight tracking-tight text-light text-3xl lg:text-5xl 2xl:text-6xl max-w-5xl">
            {{ __('The market is full of software built fast, sold cheap, and replaced in two years.') }} <br>
            <span class="text-light/25"> {{ __("I don't work that way.") }}</span>
        </p>
        <p class="mt-8 text-light/50 text-base lg:text-lg font-light leading-relaxed max-w-3xl">
            {{ __('Every choice — from the first line of code to the final interface — is made to last, to scale, and to work exactly the way your business works. Not the other way around.') }}
        </p>
    </section>


    <section class="divide-y divide-light/10">
        <div class="grid grid-cols-1 lg:grid-cols-2 px-8 lg:px-0">
            <div
                class="flex flex-col justify-center py-16 lg:py-24 lg:pl-14 lg:pr-20 lg:border-r lg:border-light/10 gap-6">
                <h2 class="font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                    {{ __('Built around you.') }}<br>
                    <span class="text-light/30">{{ __('Every time.') }}</span>
                </h2>
                <p class="text-light/55 text-sm lg:text-base leading-relaxed font-light">
                    {{ __("No templates, no shortcuts, no \"we adapt what we have\". Every project starts from a blank page and a conversation. Your processes, your logic, your needs become the blueprint. The result is software that fits you perfectly — because it was designed exclusively for you.") }}
                </p>
                <x-chip class="w-fit" size="xs">{{ __('TAILOR-MADE') }}</x-chip>
            </div>
            <div class="hidden lg:flex items-center justify-center p-14">
                <div class="grid grid-cols-2 gap-4 w-full max-w-xs opacity-20">
                    @for ($i = 0; $i < 16; $i++)
                        <div class="h-12 border border-light/40 @if ($i % 3 === 0) bg-light/5 @endif">
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 px-8 lg:px-0">
            <div class="hidden lg:flex items-center justify-center p-14 border-r border-light/10">
                <div class="flex flex-col gap-3 w-full max-w-xs opacity-25">
                    @foreach ([85, 100, 72, 100, 91, 100] as $w)
                        <div class="flex items-center gap-3">
                            <div class="h-px bg-light/60 flex-1" style="width: {{ $w }}%"></div>
                            <span class="text-light/50 text-xs font-mono">{{ $w }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex flex-col justify-center py-16 lg:py-24 lg:pl-20 lg:pr-14 gap-6">
                <h2 class="font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                    {{ __('It works.') }}<br>
                    <span class="text-light/30">{{ __('And it keeps working.') }}</span>
                </h2>
                <p class="text-light/55 text-sm lg:text-base leading-relaxed font-light">
                    {{ __('Every feature is covered by automated tests that run continuously. When something changes, the system immediately signals any problem — before it ever reaches you. Less bugs, fewer surprises, more peace of mind.') }}
                </p>
                <x-chip-list size="xs" :entries="[
                    __('Unit Testing'),
                    __('End-to-end Testing'),
                    __('Static Analysis'),
                    __('Code Coverage'),
                    __('Mutation Testing'),
                ]" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 px-8 lg:px-0">
            <div
                class="flex flex-col justify-center py-16 lg:py-24 lg:pl-14 lg:pr-20 lg:border-r lg:border-light/10 gap-6">
                <h2 class="font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                    {!! __('Built to grow <br> :tag with you. :close_tag', [
                        'tag' => '<span class="text-light/30">',
                        'close_tag' => '</span>',
                    ]) !!}
                </h2>
                <p class="text-light/55 text-sm lg:text-base leading-relaxed font-light">
                    {{ __('A good database is invisible — until you need it to scale. I design data structures that are flexible and extensible. When your business grows, adds products, changes processes: the software adapts without being rewritten from scratch.') }}
                </p>
                <p class="text-light/55 text-sm lg:text-base leading-relaxed font-light">
                    {{ __("And when your needs evolve — new markets, new processes, new integrations — I'm there. Not just as a developer, but as a technical partner who already knows your system inside out and knows how to take it further.") }}
                </p>
                <x-chip-list size="xs" :entries="[
                    __('Task Automation'),
                    __('Zero Manual Errors'),
                    __('Time Saved'),
                    __('Scheduled Reports'),
                    __('Auto Notifications'),
                    __('Seamless Sync'),
                ]" />
            </div>
            <div class="hidden lg:flex items-center justify-center p-14">
                <div class="relative w-48 h-48 opacity-15">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-20 h-8 border border-light/60"></div>
                    <div class="absolute top-1/2 left-0 -translate-y-1/2 w-20 h-8 border border-light/60"></div>
                    <div class="absolute top-1/2 right-0 -translate-y-1/2 w-20 h-8 border border-light/60"></div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-20 h-8 border border-light/60"></div>
                    <div class="absolute top-8 left-1/2 -translate-x-1/2 w-px h-32 bg-light/40">
                    </div>
                    <div class="absolute top-1/2 left-20 -translate-y-1/2 w-8 h-px bg-light/40"></div>
                </div>
            </div>
        </div>

        <div class="px-8 lg:px-14 py-20 lg:py-28 overflow-hidden">
            <div class="z-10 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-6">
                    <x-ping-dot>
                        <span
                            class="text-emerald-400/70 text-xs tracking-widest uppercase">{{ __('AI-Assisted') }}</span>
                    </x-ping-dot>

                    <h2 class="font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                        {{ __('The best of tech,') }}<br>
                        <span class="text-light/30">{{ __('in your service.') }}</span>
                    </h2>
                    <p class="text-light/55 text-sm lg:text-base leading-relaxed font-light">
                        {{ __('I use specialized AI tools to write better code, faster. Not to replace judgment — to amplify it. Higher quality in less time, fewer oversights, more focus on what actually matters for your project. AI as a precision tool, not a shortcut.') }}
                    </p>
                    <x-chip-list size="xs" :entries="[__('AI'), __('Claude')]" />
                </div>
                <div
                    class="relative text-light/25 font-black uppercase text-8xl lg:text-9xl leading-none tracking-tighter text-right select-none">
                    AI
                    <div
                        class="absolute right-8 lg:right-14 top-1/2 -translate-y-1/2 translate-x-1/2 w-96 h-96 bg-emerald-400/15 rounded-full blur-3xl pointer-events-none">
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 px-8 lg:px-0">
            <div class="hidden lg:flex items-center justify-center p-14 border-r border-light/10">
                <div class="w-full max-w-xs space-y-3 opacity-20">
                    <div class="h-10 bg-light/20 w-full"></div>
                    <div class="flex gap-3">
                        <div class="h-20 bg-light/10 flex-1"></div>
                        <div class="h-20 bg-light/10 flex-1"></div>
                        <div class="h-20 bg-light/10 flex-1"></div>
                    </div>
                    <div class="h-6 bg-light/10 w-3/4"></div>
                    <div class="h-6 bg-light/10 w-1/2"></div>
                    <div class="h-10 bg-light/20 w-2/5"></div>
                </div>
            </div>
            <div class="flex flex-col justify-center py-16 lg:py-24 lg:pl-20 lg:pr-14 gap-6">
                <h2 class="font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                    {{ __('Interfaces people') }}<br>
                    <span class="text-light/30">{{ __('actually enjoy.') }}</span>
                </h2>
                <p class="text-light/55 text-sm lg:text-base leading-relaxed font-light">
                    {{ __('The best software is the one nobody notices — because it works exactly as expected, immediately. Clean, modern, intuitive interfaces: your team learns them in minutes, not weeks. Less training, less frustration, more productivity from day one.') }}
                </p>
                <x-chip-list size="xs" :entries="[__('Tailwind'), __('Livewire'), __('Vue'), __('Filament')]" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 px-8 lg:px-0">
            <div
                class="flex flex-col justify-center py-16 lg:py-24 lg:pl-14 lg:pr-20 lg:border-r lg:border-light/10 gap-6">
                <h2 class="font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                    {!! __('Your time is <br> :tag too valuable for repetition. :close_tag', [
                        'tag' => '<span class="text-light/30">',
                        'close_tag' => '</span>',
                    ]) !!}

                </h2>
                <p class="text-light/55 text-sm lg:text-base leading-relaxed font-light">
                    {{ __('Sending reports, syncing data, generating documents, sending notifications — anything that repeats can be automated. I identify these friction points and eliminate them. The software works for you, not the other way around.') }}
                </p>
                <x-chip-list size="xs" :entries="[
                    __('Scalable'),
                    __('Extensible'),
                    __('Future-Proof'),
                    __('Modular'),
                    __('API-Ready'),
                    __('No Rewrites'),
                ]" />
            </div>
            <div class="hidden lg:flex items-center justify-center p-14">
                <div class="w-full max-w-xs opacity-15 space-y-4">
                    @foreach ([1, 2, 3] as $row)
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 border border-light/60 flex items-center justify-center">
                                <div class="w-2 h-2 bg-light/60"></div>
                            </div>
                            <div class="h-px flex-1 bg-light/40"></div>
                            <div class="w-8 h-8 border border-light/60"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-light/10">
            <div class="px-8 lg:px-14 py-16 lg:py-24 space-y-6">
                <h2 class="font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-5xl">
                    {{ __('Clean data in.') }}<br>
                    <span class="text-light/30">{{ __('Clean data out.') }}</span>
                </h2>
                <p class="text-light/55 text-sm lg:text-base leading-relaxed font-light">
                    {{ __('Incorrect data is more dangerous than missing data. Every input is validated, sanitized, and verified before being processed. This protects your database, your reports, and your decisions.') }}
                </p>
                <x-chip-list size="xs" :entries="[
                    __('No Corrupted Data'),
                    __('Input Validation'),
                    __('Error Prevention'),
                    __('Trusted Reports'),
                    __('Consistent Data'),
                ]" />
            </div>
            <div class="px-8 lg:px-14 py-16 lg:py-24 space-y-6">
                <h2 class="font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-5xl mb-6">
                    {{ __('Modern.') }}<br>
                    <span class="text-light/30">{{ __('Proven. Reliable.') }}</span>
                </h2>
                <p class="text-light/55 text-sm lg:text-base leading-relaxed font-light">
                    {{ __('Mature, well-documented technologies actively maintained by large communities. No experiments on your product. Everything containerized and deployable anywhere — today as in three years.') }}
                </p>
                <x-chip-list size="xs" :entries="[
                    __('Laravel'),
                    __('Livewire'),
                    __('Filament'),
                    __('Tailwind'),
                    __('Vue'),
                    __('Docker'),
                    __('TypeScript'),
                    __('PHP'),
                    __('Nightwatch'),
                    __('Sentry'),
                    __('StatusCake'),
                ]" />
            </div>
        </div>

    </section>

    <section class="bg-dark border-t border-light/10 px-8 lg:px-14 py-24 lg:py-36">
        <div class="max-w-5xl mx-auto">
            <p class="font-semibold leading-tight tracking-tight text-light text-3xl lg:text-5xl 2xl:text-6xl mb-16">
                {{ __("You don't need faster software.") }}<br>
                <span class="text-light/25">{{ __('You need software that actually works.') }}</span>
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a wire:navigate href="{{ route('contacts') }}"
                    class="group relative overflow-hidden border border-light text-light flex items-center justify-between gap-8 px-8 py-5 hover:text-dark transition-colors duration-300">
                    <span
                        class="absolute inset-0 bg-light translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
                    <span
                        class="relative font-semibold uppercase tracking-widest text-sm z-10">{{ __("Let's talk") }}</span>
                    <x-ri-arrow-right-long-line class="z-10 w-5" />
                </a>

                <a wire:navigate href="{{ route('projects') }}"
                    class="group relative overflow-hidden border border-light/20 text-light/50 flex items-center justify-between gap-8 px-8 py-5 hover:text-dark hover:border-light transition-colors duration-300">
                    <span
                        class="absolute inset-0 bg-light translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-[cubic-bezier(.77,0,.18,1)] -z-0"></span>
                    <span
                        class="relative font-semibold uppercase tracking-widest text-sm z-10">{{ __('See projects') }}</span>
                    <x-ri-arrow-right-long-line class="z-10 w-5" />
                </a>
            </div>
        </div>
    </section>
</div>
