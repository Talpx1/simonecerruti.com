{{--
    Cookie Consent Banner – Livewire v4 compatible
    ───────────────────────────────────────────────
    REGOLA v4: un solo elemento radice, nessun wire:key fuori dai loop,
    nessun @if che avvolge l'elemento radice stesso.
--}}
<div class="fixed bottom-4 left-2 right-2 lg:left-4 z-50">

    {{-- ── Banner ─────────────────────────────────────────────────────── --}}
    @if ($show)
        <div role="dialog" aria-modal="true" aria-label="{{ __('Cookie consent') }}"
            class="w-full max-w-sm border border-gray-200 dark:border-gray-700 bg-light dark:bg-gray-900 shadow-2xl overflow-hidden">
            {{-- Coloured top strip --}}
            <div class="h-1 w-full bg-light"></div>

            <div class="p-5">

                {{-- ── Summary view ──────────────────────────────────────── --}}
                @if (!$showPreferences)
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl" aria-hidden="true">🍪</span>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-light">
                            {{ __('We use cookies') }}
                        </h2>
                    </div>

                    <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('We use cookies and similar technologies to improve your experience, analyse traffic and enable certain features. Some are strictly necessary; others require your consent.') }}
                        <a href="{{ route('cookie_policy') }}"
                            class="underline text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 ml-1"
                            target="_blank">{{ __('Cookie Policy') }}</a>
                        ·
                        <a href="{{ route('privacy_policy') }}"
                            class="underline text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                            {{ __('Privacy Policy') }}</a>
                    </p>

                    <div class="flex flex-col gap-2">
                        <button wire:click="acceptAll" type="button"
                            class="w-full bg-gray-900 dark:bg-light text-light dark:text-gray-900 text-xs font-semibold py-2.5 px-4 hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-light">
                            {{ __('Accept all') }}
                        </button>

                        <div class="flex gap-2">
                            <button wire:click="rejectAll" type="button"
                                class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium py-2 px-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                                {{ __('Reject all') }}
                            </button>

                            <button wire:click="openPreferences" type="button"
                                class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium py-2 px-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                                {{ __('Manage preferences') }}
                            </button>
                        </div>
                    </div>
                @else
                    {{-- ── Preferences panel ─────────────────────────────────── --}}

                    <div class="flex items-center gap-2 mb-4">
                        <button wire:click="$set('showPreferences', false)" type="button"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                            aria-label="{{ __('Back') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-light">
                            {{ __('Manage cookie preferences') }}
                        </h2>
                    </div>

                    <div class="space-y-3 mb-5">

                        {{-- Necessary --}}
                        <div
                            class="flex items-start justify-between gap-3 border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800 p-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-900 dark:text-light mb-0.5">
                                    {{ __('Strictly necessary') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                    {{ __('Session management, CSRF protection, consent record. Cannot be disabled.') }}
                                </p>
                            </div>
                            <span
                                class="mt-0.5 flex-shrink-0 inline-flex items-center rounded-full bg-green-100 dark:bg-green-900 px-2 py-0.5 text-xs font-medium text-green-800 dark:text-green-200">
                                {{ __('Always on') }}
                            </span>
                        </div>

                        {{-- Analytics --}}
                        <div
                            class="flex items-start justify-between gap-3 border border-gray-100 dark:border-gray-800 p-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-900 dark:text-light mb-0.5">
                                    {{ __('Analytics') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                    {{ __('Microsoft Clarity – heatmaps, session recordings, behavioural analytics.') }}
                                </p>
                            </div>
                            <x-layouts.public.cookie-toggle wire:model="analytics" id="toggle-analytics" />
                        </div>

                        {{-- Functional --}}
                        <div
                            class="flex items-start justify-between gap-3 border border-gray-100 dark:border-gray-800 p-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-900 dark:text-light mb-0.5">
                                    {{ __('Functional') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                    {{ __('Koalenda – external scheduling link. Enables tracking of referral source on their platform.') }}
                                </p>
                            </div>
                            <x-layouts.public.cookie-toggle wire:model="functional" id="toggle-functional" />
                        </div>

                        {{-- Marketing --}}
                        <div
                            class="flex items-start justify-between gap-3 border border-gray-100 dark:border-gray-800 p-3 opacity-60">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-light">
                                        {{ __('Marketing') }}
                                    </p>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">
                                        ({{ __('coming soon') }})
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                    {{ __('Newsletter & Mailchimp – email marketing communications. Not yet active.') }}
                                </p>
                            </div>
                            <x-layouts.public.cookie-toggle wire:model="marketing" id="toggle-marketing" />
                        </div>

                    </div>

                    <button wire:click="savePreferences" type="button"
                        class="w-full bg-gray-900 dark:bg-light text-light dark:text-gray-900 text-xs font-semibold py-2.5 px-4 hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                        {{ __('Save my preferences') }}
                    </button>
                @endif

            </div>

            {{-- Footer note --}}
            <div class="px-5 pb-4">
                <p class="text-center text-gray-400 dark:text-gray-600" style="font-size: 0.6rem; line-height: 1.4;">
                    {{ __('You can change your choices at any time from our') }}
                    <a href="{{ route('cookie_policy') }}"
                        class="underline hover:text-gray-600 dark:hover:text-gray-400">{{ __('Cookie Policy') }}</a>.
                    {{ __('Refusing optional cookies will not affect basic functionality.') }}
                </p>
            </div>

        </div>
    @endif

</div>
