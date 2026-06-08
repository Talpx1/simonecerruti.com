<div>
    {{-- ============================== HERO + FORM ============================== --}}
    <section data-pan="section-impression-contacts" class="relative overflow-hidden pt-12 lg:pt-16 pb-16 lg:pb-24">
        <div class="relative max-w-7xl mx-auto px-8 lg:px-14">

            <div data-reveal class="max-w-2xl">
                <x-eyebrow>{{ __('Let\'s talk about your project') }}</x-eyebrow>

                <h1
                    class="mt-6 font-black uppercase leading-none tracking-tighter text-light text-6xl sm:text-7xl lg:text-8xl">
                    {{ __('Contacts') }}
                </h1>

                <p class="mt-7 text-light text-xl lg:text-2xl font-medium leading-snug max-w-[30ch]">
                    {{ __('It starts here. The rest, we build together.') }}
                </p>
            </div>

            <div class="mt-12 lg:mt-20 grid grid-cols-1 lg:grid-cols-[1.5fr_0.9fr] gap-10 lg:gap-16 items-start">
                {{-- form --}}
                <div>
                    <livewire:components.contact-form />
                </div>

                {{-- aside --}}
                <aside class="border border-light/15 flex flex-col">
                    <div class="p-7 lg:p-8">
                        <x-ping-dot>{{ __('Available') }}</x-ping-dot>
                        <p class="mt-4 text-light/55 text-base leading-relaxed max-w-[26ch]">
                            {{ __('Open to new projects and collaborations for the coming quarter.') }}
                        </p>
                    </div>

                    <div class="p-7 lg:p-8 border-t border-light/15">
                        <p class="text-xs uppercase tracking-[0.2em] text-light/40 mb-6">{{ __('What happens next') }}</p>

                        @php
                            $steps = [
                                [__('I reply within 24 hours.'), __('I read it carefully and get back to you in person, no auto-responders.')],
                                [__('Free introductory call.'), __('15 minutes to understand goals and priorities, no strings attached.')],
                                [__('Tailor-made proposal.'), __('A concrete estimate, built around your project and not off a price list.')],
                            ];
                        @endphp

                        <div class="flex flex-col">
                            @foreach ($steps as $i => [$step_lead, $step_body])
                                <div class="flex gap-4 items-start py-4 border-t border-light/15 first:border-t-0 first:pt-0">
                                    <span class="text-xs text-light/40 pt-1 shrink-0 w-6">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                    <p class="text-[15px] leading-snug">
                                        <b class="font-semibold text-light">{{ $step_lead }}</b>
                                        <span class="text-light/55"> {{ $step_body }}</span>
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- ============================== DIRECT CHANNELS ============================== --}}
    <section class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <div class="mb-12 lg:mb-16">
                <x-eyebrow>{{ __('Prefer another channel?') }}</x-eyebrow>
                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl max-w-[16ch]">
                    {!! __('Let\'s talk<br>however suits you') !!}
                </h2>
            </div>

            @php
                $channels = [
                    [
                        'icon' => 'heroicon-o-phone',
                        'title' => __('Book a call'),
                        'body' => __('Rather talk than write? Pick a slot and we\'ll have a chat.'),
                        'link_label' => __('Book a call'),
                        'href' => config()->string('company.contacts.koalenda_url'),
                        'pan' => 'cta-contacts-call',
                        'external' => true,
                    ],
                    [
                        'icon' => 'heroicon-o-envelope',
                        'title' => __('Email me'),
                        'body' => __('For detailed requests or files to attach, write to me directly.'),
                        'link_label' => config()->string('company.contacts.email'),
                        'href' => 'mailto:' . config()->string('company.contacts.email'),
                        'pan' => 'cta-contacts-email',
                        'external' => false,
                    ],
                    [
                        'icon' => 'fab-whatsapp',
                        'title' => __('Text me on WhatsApp'),
                        'body' => __('A quick question or an informal first contact? Send me a message.'),
                        'link_label' => __('Start a Chat'),
                        'href' => sprintf(
                            'https://wa.me/%s?text=%s',
                            config()->string('company.contacts.whatsapp.number'),
                            urlencode(__(config()->string('company.contacts.whatsapp.default_message'))),
                        ),
                        'pan' => 'cta-contacts-whatsapp',
                        'external' => true,
                    ],
                ];
            @endphp

            <div data-reveal class="grid grid-cols-1 lg:grid-cols-3 border-t border-l border-light/15">
                @foreach ($channels as $channel)
                    <article class="p-8 lg:p-10 border-r border-b border-light/15 flex flex-col">
                        <span class="w-10 h-10 border border-light/30 flex items-center justify-center text-light mb-7">
                            @svg($channel['icon'], 'w-5')
                        </span>
                        <h3 class="font-bold tracking-tight leading-tight text-light text-xl lg:text-2xl mb-3">{{ $channel['title'] }}</h3>
                        <p class="text-light/55 text-sm leading-relaxed font-light mb-8 max-w-[30ch]">{{ $channel['body'] }}</p>

                        <a href="{{ $channel['href'] }}" @if ($channel['external']) target="_blank" rel="noopener" @endif
                            data-pan="{{ $channel['pan'] }}"
                            class="group/clink mt-auto inline-flex items-center gap-2 text-xs uppercase tracking-[0.08em] text-light/55 hover:text-light transition-colors">
                            <span class="underline underline-offset-4">{{ $channel['link_label'] }}</span>
                            <x-ri-arrow-right-long-line class="w-3.5 shrink-0 transition-transform group-hover/clink:translate-x-1" />
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== SOCIAL ============================== --}}
    <section class="border-t border-light/15 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-8 lg:px-14">
            <div class="mb-12 lg:mb-16">
                <x-eyebrow>{{ __('Follow me') }}</x-eyebrow>
                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl">
                    {{ __('Where to find me') }}
                </h2>
            </div>

            <x-social-links variant="grid" placement="contacts" />
        </div>
    </section>
</div>
