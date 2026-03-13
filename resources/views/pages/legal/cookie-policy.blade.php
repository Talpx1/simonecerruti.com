<div class="min-h-screen bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        {{-- Header --}}
        <header class="mb-12 border-b border-gray-200 dark:border-gray-800 pb-8">
            <p class="text-sm font-medium uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-3">
                {{ __('Legal') }}
            </p>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ __('Cookie Policy') }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Last updated:') }} <time>13/03/2026</time>
            </p>
        </header>

        <div class="space-y-10">

            {{-- Intro --}}
            <section>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('This document informs Users about the technologies that help this Website to achieve the purposes described below. Such technologies allow the Owner to access and store information (for example by using a Cookie) or use resources (for example by running a script) on a User\'s device as they interact with this Website.') }}
                </p>
            </section>

            {{-- What are cookies --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-amber-500 rounded-full inline-block"></span>
                    {{ __('What are Cookies and similar technologies?') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300 mb-3">
                    {{ __('Cookies are small files stored on your device. They serve many functions: they can help this Website remember your preferences, understand how you use the Website, and serve you relevant information. In addition to Cookies, this Website may use other technologies such as web beacons, pixel tags, localStorage, and sessionStorage.') }}
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-2">{{ __('Session Cookies') }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Temporary cookies that expire when you close your browser. They do not collect information from your computer.') }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-2">
                            {{ __('Persistent Cookies') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Remain on your device for a set period. They are activated each time you visit the Website that created them.') }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-2">
                            {{ __('First-party Cookies') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Set directly by this Website for its own purposes.') }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-2">
                            {{ __('Third-party Cookies') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Set by external services used by this Website (e.g. analytics, embedded content).') }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- How we use cookies --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-amber-500 rounded-full inline-block"></span>
                    {{ __('How this Website uses Cookies and Trackers') }}
                </h2>

                {{-- Table --}}
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide text-xs">
                                    {{ __('Tracker / Service') }}</th>
                                <th
                                    class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide text-xs">
                                    {{ __('Type') }}</th>
                                <th
                                    class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide text-xs">
                                    {{ __('Purpose') }}</th>
                                <th
                                    class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide text-xs">
                                    {{ __('Consent required?') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-950">
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Laravel Session</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('Session Cookie (1st party)') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('Maintains user session, CSRF protection, flash messages.') }}</td>
                                <td class="px-4 py-3"><span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">{{ __('No – technical') }}</span>
                                </td>
                            </tr>
                            <tr class="bg-gray-50 dark:bg-gray-900">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Font Bunny</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('External resource (EU)') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('Loads web fonts from Bunny.net CDN without tracking.') }}</td>
                                <td class="px-4 py-3"><span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">{{ __('No – no personal data') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Microsoft Clarity</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('Analytics (3rd party)') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('Heatmaps, session recordings, behavioural analytics.') }}</td>
                                <td class="px-4 py-3"><span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200">{{ __('Yes – analytics') }}</span>
                                </td>
                            </tr>
                            <tr class="bg-gray-50 dark:bg-gray-900">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Google reCAPTCHA</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('Anti-spam (3rd party)') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('Protects forms from bots and automated submissions.') }}</td>
                                <td class="px-4 py-3"><span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">{{ __('No – legitimate interest') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">Koalenda</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('External link (3rd party)') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('Redirects to Koalenda\'s external platform. No cookies set by this Website.') }}
                                </td>
                                <td class="px-4 py-3"><span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200">{{ __('No – external redirect') }}</span>
                                </td>
                            </tr>
                            <tr class="bg-gray-50 dark:bg-gray-900">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">sessionStorage</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('Browser storage (1st party)') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('Temporary in-tab storage. Cleared on tab close.') }}</td>
                                <td class="px-4 py-3"><span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">{{ __('No – technical') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">localStorage</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('Browser storage (1st party)') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ __('Persistent local storage for preferences (e.g. theme, language).') }}</td>
                                <td class="px-4 py-3"><span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">{{ __('No – preferences') }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Cookie categories --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-amber-500 rounded-full inline-block"></span>
                    {{ __('Cookie Categories') }}
                </h2>
                <div class="space-y-4">
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl p-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ __('Strictly Necessary') }}
                        </h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ __('These cookies are essential for the Website to function correctly and cannot be switched off. They are usually set in response to actions you take, such as setting your privacy preferences, logging in, or filling in forms. These cookies do not store personally identifiable information.') }}
                        </p>
                    </div>
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl p-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                            {{ __('Functional / Preference') }}</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ __('These cookies enable enhanced functionality and personalisation. They may be set by us or by third-party providers whose services we have added to our pages. If you do not allow these cookies, some or all of these services may not function properly.') }}
                        </p>
                    </div>
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl p-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                            {{ __('Analytics / Performance') }}</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ __('These cookies (e.g. Microsoft Clarity) allow us to count visits and traffic sources so we can measure and improve the performance of our Website. They help us know which pages are the most and least popular. All information these cookies collect is aggregated.') }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- How to manage --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-amber-500 rounded-full inline-block"></span>
                    {{ __('How to manage Cookies') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300 mb-4">
                    {{ __('Users can set or amend their web browser controls to accept or refuse cookies. If a User chooses to reject cookies, they may still use our Website though their access to some functionality may be restricted. As the means by which a User can refuse cookies through their web browser controls vary from browser-to-browser, Users should visit their browser\'s help menu for more information.') }}
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach ([['Chrome', 'https://support.google.com/chrome/answer/95647'], ['Firefox', 'https://support.mozilla.org/en-US/kb/enhanced-tracking-protection-firefox-desktop'], ['Safari', 'https://support.apple.com/guide/safari/manage-cookies-sfri11471'], ['Edge', 'https://support.microsoft.com/en-us/windows/manage-cookies-in-microsoft-edge']] as [$browser, $url])
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                            class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            {{ $browser }}
                            <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- Owner info --}}
            <section class="bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-amber-900 dark:text-amber-100 mb-2">
                    {{ __('Data Controller') }}
                </h2>
                <p class="text-sm text-amber-800 dark:text-amber-200">
                    {{ config('company.name') }} – {{ config('company.vat') }}<br>
                    <a href="mailto:{{ config('company.contacts.email') }}"
                        class="underline">{{ config('company.contacts.email') }}</a>
                </p>
            </section>

        </div>
    </div>
</div>
