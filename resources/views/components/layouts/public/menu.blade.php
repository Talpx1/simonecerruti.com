<div x-show="isMenuOpen" x-cloak x-transition:enter="transition duration-500 ease-[cubic-bezier(.77,0,.18,1)]"
    x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0"
    x-transition:leave="transition duration-400 ease-[cubic-bezier(.77,0,.18,1)]" x-transition:leave-start="translate-y-0"
    x-transition:leave-end="-translate-y-full" x-effect="document.body.style.overflow = isMenuOpen ? 'hidden' : ''"
    class="fixed inset-0 z-90 bg-dark text-light flex flex-col overflow-hidden" role="dialog"
    aria-label="Menu principale" :aria-hidden="!isMenuOpen">

    <div class="border-b border-light/10 h-[100px] min-h-[100px] max-h-[100px]"></div>

    <div class="flex-1 grid grid-cols-1 lg:grid-cols-[1fr_320px] min-h-0">

        <nav class="flex flex-col justify-center px-8 lg:px-14 py-12 border-r border-light/10">
            <ul class="space-y-1">
                @php
                    $routes = [
                        [__('Home'), route('home')],
                        [__('About'), route('about')],
                        [__('Projects'), route('projects')],
                        [__('How I work'), route('how_i_work')],
                        [__('Contacts'), route('contacts')],
                    ];
                @endphp
                @foreach ($routes as $i => [$label, $route])
                    <li>
                        <a wire:navigate href="{{ $route }}" x-on:click="isMenuOpen=false"
                            class="group flex items-baseline gap-0 hover:gap-3 transition-all duration-300">

                            <span
                                class="menu-item text-xs tracking-widest text-light/40 uppercase
                                     w-0 overflow-hidden shrink-0
                                     group-hover:w-8 group-hover:opacity-100
                                     transition-all duration-300
                                     opacity-0 translate-y-8">
                                {{ str_pad((string) $i + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            <span
                                class="menu-item block font-semibold uppercase tracking-tight leading-none text-light translate-y-8 transition-all duration-500"
                                style="font-size: clamp(44px, 7vw, 100px);">

                                {{ $label }}

                                <span
                                    class="block h-0.5 bg-current scale-x-0 origin-left
                                         transition-transform duration-300 group-hover:scale-x-100">
                                </span>
                            </span>

                        </a>
                    </li>
                @endforeach

            </ul>
        </nav>

        <aside class="flex flex-col justify-between px-10 py-12 gap-8 lg:gap-0">

            <div>
                <x-ping-dot>{{ __('Available') }}</x-ping-dot>
                <p class="text-xs text-light/30 tracking-wide leading-relaxed">
                    {!! __('Open to new projects<br>and collaborations.') !!}
                </p>
            </div>

            <div>
                <p class="text-[10px] tracking-widest uppercase text-light/30 mb-3">{{ __('Direct contact') }}</p>
                <a href="mailto:{{ config('company.contacts.email') }}"
                    class="text-sm tracking-wider text-light hover:opacity-60 transition-opacity duration-200 break-all underline underline-offset-1 decoration-1">
                    {{ config('company.contacts.email') }}
                </a>
            </div>

            <div>
                <p class="text-[10px] tracking-widest uppercase text-light/30 mb-5">{{ __('Social') }}</p>
                <div class="flex flex-row lg:flex-col justify-between lg:gap-3">
                    <div>
                        <a target="_blank" rel="noopener" href="{{ config('company.socials.linkedin.link') }}">
                            <div
                                class="flex gap-2 items-center text-xs uppercase tracking-widest text-light/40 hover:text-light hover:tracking-[.2em] transition-all duration-200">
                                <x-fab-linkedin class="w-5 lg:w-3" />
                                <span class="hidden lg:inline">LinkedIn</span>
                            </div>
                        </a>
                    </div>
                    <div>
                        <a target="_blank" rel="noopener" href="{{ config('company.socials.instagram.link') }}">
                            <div
                                class="flex gap-2 items-center text-xs uppercase tracking-widest text-light/40 hover:text-light hover:tracking-[.2em] transition-all duration-200">
                                <x-fab-instagram class="w-5 lg:w-3" />
                                <span class="hidden lg:inline">Instagram</span>
                            </div>
                        </a>
                    </div>
                    <div>
                        <a target="_blank" rel="noopener" href="{{ config('company.socials.github.link') }}">
                            <div
                                class="flex gap-2 items-center text-xs uppercase tracking-widest text-light/40 hover:text-light hover:tracking-[.2em] transition-all duration-200">
                                <x-fab-github class="w-5 lg:w-3" />
                                <span class="hidden lg:inline">GitHub</span>
                            </div>
                        </a>
                    </div>
                    <div>
                        <a target="_blank" rel="noopener" href="{{ config('company.socials.bluesky.link') }}">
                            <div
                                class="flex gap-2 items-center text-xs uppercase tracking-widest text-light/40 hover:text-light hover:tracking-[.2em] transition-all duration-200">
                                <x-fab-bluesky class="w-5 lg:w-3" />
                                <span class="hidden lg:inline">BlueSky</span>
                            </div>
                        </a>
                    </div>
                    <div>
                        <a target="_blank" rel="noopener" href="{{ config('company.socials.x.link') }}">
                            <div
                                class="flex gap-2 items-center text-xs uppercase tracking-widest text-light/40 hover:text-light hover:tracking-[.2em] transition-all duration-200">
                                <x-fab-x-twitter class="w-5 lg:w-3" />
                                <span class="hidden lg:inline">X / Twitter</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <p class="text-[10px] text-light/20 tracking-widest uppercase">
                © {{ date('Y') }} — {{ __('All rights reserved.') }}
            </p>

        </aside>
    </div>

</div>
