<div class="min-h-screen bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        {{-- Header --}}
        <header class="mb-12 border-b border-gray-200 dark:border-gray-800 pb-8">
            <p class="text-sm font-medium uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-3">
                {{ __('Legal') }}
            </p>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ __('Privacy Policy') }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Last updated:') }} <time>13/03/2026</time>
            </p>
        </header>

        <div class="prose-container space-y-10">

            {{-- Intro --}}
            <section>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('This document informs Users about the technologies that help this Website to achieve the purposes described below. Such technologies allow the Owner to access and store information (for example by using a Cookie) or use resources (for example by running a script) on a User\'s device as they interact with this Website.') }}
                </p>
                <p class="mt-4 text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('For simplicity, all such technologies are defined as "Trackers" within this document – unless there is a reason to differentiate. For example, while Cookies can be used on both web and mobile browsers, it would be inaccurate to talk about Cookies in the context of mobile apps as they are a browser-based Tracker. For this reason, within this document, the term Cookies is only used where it is specifically meant to indicate that particular type of Tracker.') }}
                </p>
            </section>

            {{-- Owner --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-blue-500 rounded-full inline-block"></span>
                    {{ __('Data Controller (Owner)') }}
                </h2>
                <div
                    class="bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 space-y-2">
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-white">{{ __('Company Name:') }}</span>
                        {{ config('company.name') }}
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-white">{{ __('VAT Number:') }}</span>
                        {{ config('company.vat') }}
                    </p>
                    {{-- <p class="text-sm text-gray-700 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-white">{{ __('Address:') }}</span>
                        {{ config('company.address') }}
                    </p> --}}
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-white">{{ __('Contact Email:') }}</span>
                        <a href="mailto:{{ config('company.contacts.email') }}"
                            class="text-blue-600 dark:text-blue-400 hover:underline">
                            {{ config('company.contacts.email') }}
                        </a>
                    </p>
                </div>
            </section>

            {{-- Types of Data Collected --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-blue-500 rounded-full inline-block"></span>
                    {{ __('Types of Data Collected') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300 mb-4">
                    {{ __('Among the types of Personal Data that this Website collects, by itself or through third parties, there are: first name; last name; company name; email address; phone number; IP address; Usage Data (browser User Agent).') }}
                </p>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('Complete details on each type of Personal Data collected are provided in the dedicated sections of this privacy policy or by specific explanation texts displayed prior to the Data collection. Personal Data may be freely provided by the User, or, in case of Usage Data, collected automatically when using this Website.') }}
                </p>
            </section>

            {{-- Processing Place --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-blue-500 rounded-full inline-block"></span>
                    {{ __('Place of Processing') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('The Data is processed at the Owner\'s operating offices and in any other places where the parties involved in the processing are located. The Website is hosted on servers provided by Hetzner Online GmbH (Germany/EU). Backups are stored on Google Drive.') }}
                </p>
            </section>

            {{-- Purposes of Processing --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-blue-500 rounded-full inline-block"></span>
                    {{ __('Purposes of Processing and Legal Basis') }}
                </h2>
                <div class="space-y-6">

                    {{-- Contact Form --}}
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                        <div
                            class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                {{ __('Contact Form (Proprietary)') }}
                            </h3>
                        </div>
                        <div class="px-5 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p>{{ __('By filling in the contact form with their Data, the User authorises this Website to use such details to reply to requests for information, quotes, or any other kind of request as indicated by the form\'s header.') }}
                            </p>
                            <p><span class="font-medium">{{ __('Data collected:') }}</span>
                                {{ __('First name, last name, company name, email address, phone number, IP address, User Agent.') }}
                            </p>
                            <p><span class="font-medium">{{ __('Legal basis:') }}</span>
                                {{ __('Consent (Art. 6(1)(a) GDPR); legitimate interest in responding to enquiries.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Font Bunny --}}
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                        <div
                            class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                {{ __('Font Bunny (Typography)') }}
                            </h3>
                        </div>
                        <div class="px-5 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p>{{ __('Font Bunny is a GDPR-compliant font hosting service. Web fonts are loaded from Font Bunny\'s servers. No personal data is transmitted to third parties outside the EU.') }}
                            </p>
                            <p><span class="font-medium">{{ __('Service provider:') }}</span> Bunny.net (EU)</p>
                            <p><span class="font-medium">{{ __('Privacy policy:') }}</span> <a
                                    href="https://bunny.net/privacy" target="_blank" rel="noopener noreferrer"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">bunny.net/privacy</a></p>
                        </div>
                    </div>

                    {{-- Koalenda --}}
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                        <div
                            class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                {{ __('Koalenda (Appointment Scheduling)') }}
                            </h3>
                        </div>
                        <div class="px-5 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p>{{ __('This Website contains a link to Koalenda\'s scheduling platform. By clicking the link, the User is redirected to an external website governed by Koalenda\'s own privacy policy. No data is transmitted to Koalenda unless the User actively uses their platform.') }}
                            </p>
                            <p><span class="font-medium">{{ __('Service provider:') }}</span> Koalenda</p>
                            <p><span class="font-medium">{{ __('Privacy policy:') }}</span> <a
                                    href="https://koalenda.com/privacy-policy" target="_blank" rel="noopener noreferrer"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">koalenda.com/privacy-policy</a>
                            </p>
                        </div>
                    </div>

                    {{-- Microsoft Clarity --}}
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                        <div
                            class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                {{ __('Microsoft Clarity (Analytics & Heatmaps)') }}
                            </h3>
                        </div>
                        <div class="px-5 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p>{{ __('Microsoft Clarity is a behavioural analytics tool that helps understand how Users interact with this Website via heatmaps, session recordings and click analytics. It may collect Usage Data including IP address and User Agent.') }}
                            </p>
                            <p><span class="font-medium">{{ __('Service provider:') }}</span> Microsoft Corporation
                                (USA)</p>
                            <p><span class="font-medium">{{ __('Privacy policy:') }}</span> <a
                                    href="https://privacy.microsoft.com/privacystatement" target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">privacy.microsoft.com</a>
                            </p>
                            <p><span class="font-medium">{{ __('Legal basis:') }}</span>
                                {{ __('Consent (Art. 6(1)(a) GDPR).') }}</p>
                        </div>
                    </div>

                    {{-- StatusCake --}}
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                        <div
                            class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                {{ __('StatusCake (Uptime Monitoring)') }}
                            </h3>
                        </div>
                        <div class="px-5 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p>{{ __('StatusCake is used to monitor the Website\'s availability. It makes periodic requests to this Website\'s URLs; logs may include IP addresses.') }}
                            </p>
                            <p><span class="font-medium">{{ __('Service provider:') }}</span> StatusCake (UK)</p>
                            <p><span class="font-medium">{{ __('Privacy policy:') }}</span> <a
                                    href="https://www.statuscake.com/privacy-policy/" target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">statuscake.com/privacy-policy</a>
                            </p>
                        </div>
                    </div>

                    {{-- Hetzner --}}
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                        <div
                            class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                {{ __('Hetzner (Hosting Infrastructure)') }}
                            </h3>
                        </div>
                        <div class="px-5 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p>{{ __('This Website is hosted on servers provided by Hetzner Online GmbH. As a hosting provider, Hetzner may process technical data including IP addresses in server access logs.') }}
                            </p>
                            <p><span class="font-medium">{{ __('Service provider:') }}</span> Hetzner Online GmbH,
                                Industriestr. 25, 91710 Gunzenhausen, Germany</p>
                            <p><span class="font-medium">{{ __('Privacy policy:') }}</span> <a
                                    href="https://www.hetzner.com/legal/privacy-policy" target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">hetzner.com/legal/privacy-policy</a>
                            </p>
                        </div>
                    </div>

                    {{-- Google Drive Backup --}}
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                        <div
                            class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                {{ __('Google Drive (Backup Storage)') }}
                            </h3>
                        </div>
                        <div class="px-5 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p>{{ __('Database backups are stored on Google Drive. This means that data collected through this Website (including personal data submitted via forms) may be included in encrypted backup files stored on Google\'s infrastructure.') }}
                            </p>
                            <p><span class="font-medium">{{ __('Service provider:') }}</span> Google LLC (USA)</p>
                            <p><span class="font-medium">{{ __('Privacy policy:') }}</span> <a
                                    href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">policies.google.com/privacy</a>
                            </p>
                        </div>
                    </div>

                    {{-- Google reCAPTCHA --}}
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                        <div
                            class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                {{ __('Google reCAPTCHA (Spam Protection)') }}
                            </h3>
                        </div>
                        <div class="px-5 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p>{{ __('Google reCAPTCHA is used to protect forms on this Website from spam and automated abuse. It collects hardware and software information (such as device and application data) and sends it to Google for analysis.') }}
                            </p>
                            <p><span class="font-medium">{{ __('Service provider:') }}</span> Google LLC (USA)</p>
                            <p><span class="font-medium">{{ __('Privacy policy:') }}</span> <a
                                    href="https://policies.google.com/privacy" target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">policies.google.com/privacy</a>
                            </p>
                            <p><span class="font-medium">{{ __('Legal basis:') }}</span>
                                {{ __('Legitimate interest in protecting the Website from abusive automated requests (Art. 6(1)(f) GDPR).') }}
                            </p>
                        </div>
                    </div>

                    {{-- WhatsApp --}}
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                        <div
                            class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                {{ __('WhatsApp Chat Button') }}
                            </h3>
                        </div>
                        <div class="px-5 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p>{{ __('This Website includes a button to initiate a WhatsApp chat. Clicking the button will redirect the User to WhatsApp\'s platform. Any data shared in a WhatsApp conversation is processed by Meta Platforms, Inc. under WhatsApp\'s own privacy policy.') }}
                            </p>
                            <p><span class="font-medium">{{ __('Service provider:') }}</span> Meta Platforms, Inc.
                                (USA)</p>
                            <p><span class="font-medium">{{ __('Privacy policy:') }}</span> <a
                                    href="https://www.whatsapp.com/legal/privacy-policy" target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">whatsapp.com/legal/privacy-policy</a>
                            </p>
                        </div>
                    </div>

                    {{-- Session/Local Storage --}}
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                        <div
                            class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                {{ __('Browser Storage (sessionStorage & localStorage)') }}
                            </h3>
                        </div>
                        <div class="px-5 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p>{{ __('This Website uses sessionStorage and localStorage to store data locally in the User\'s browser. sessionStorage data is deleted when the browser tab is closed; localStorage data persists until explicitly cleared. These mechanisms are used to improve Website functionality and User experience.') }}
                            </p>
                            <p><span class="font-medium">{{ __('Legal basis:') }}</span>
                                {{ __('Legitimate interest in providing technical functionality (Art. 6(1)(f) GDPR) and/or consent where required.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Laravel Nightwatch (hidden) --}}
                    {{-- HIDDEN - not yet in production
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                {{ __('Laravel Nightwatch (Application Monitoring)') }}
                            </h3>
                        </div>
                        <div class="px-5 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p>{{ __('Laravel Nightwatch is used for application performance monitoring and error tracking. It may collect technical data including request details, errors, and performance metrics.') }}</p>
                            <p><span class="font-medium">{{ __('Legal basis:') }}</span> {{ __('Legitimate interest in maintaining application stability (Art. 6(1)(f) GDPR).') }}</p>
                        </div>
                    </div>
                    --}}

                    {{-- Newsletter / Mailchimp (hidden) --}}
                    {{-- HIDDEN - not yet in production
                    <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                {{ __('Newsletter & Mailchimp (Email Marketing)') }}
                            </h3>
                        </div>
                        <div class="px-5 py-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p>{{ __('Users may subscribe to our newsletter by providing their email address. Email addresses are managed through Mailchimp, an email marketing platform. Users can unsubscribe at any time via the link in each email.') }}</p>
                            <p><span class="font-medium">{{ __('Data collected:') }}</span> {{ __('Email address, first name (optional).') }}</p>
                            <p><span class="font-medium">{{ __('Service provider:') }}</span> The Rocket Science Group LLC d/b/a Mailchimp (USA)</p>
                            <p><span class="font-medium">{{ __('Privacy policy:') }}</span> <a href="https://mailchimp.com/legal/privacy/" target="_blank" rel="noopener noreferrer" class="text-blue-600 dark:text-blue-400 hover:underline">mailchimp.com/legal/privacy</a></p>
                            <p><span class="font-medium">{{ __('Legal basis:') }}</span> {{ __('Consent (Art. 6(1)(a) GDPR).') }}</p>
                        </div>
                    </div>
                    --}}

                </div>
            </section>

            {{-- User Rights --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-blue-500 rounded-full inline-block"></span>
                    {{ __('Rights of Users') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300 mb-4">
                    {{ __('Users may exercise certain rights regarding their Data processed by the Owner. In particular, Users have the right to:') }}
                </p>
                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300 list-none">
                    @foreach ([__('Withdraw their consent at any time.'), __('Object to processing of their Data.'), __('Access their Data.'), __('Verify and seek rectification.'), __('Restrict the processing of their Data.'), __('Have their Personal Data deleted or otherwise removed.'), __('Receive their Data and have it transferred to another controller.'), __('Lodge a complaint with the competent supervisory authority (Garante per la protezione dei dati personali – www.garanteprivacy.it).')] as $right)
                        <li class="flex items-start gap-3">
                            <span
                                class="mt-1 flex-shrink-0 w-5 h-5 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            {{ $right }}
                        </li>
                    @endforeach
                </ul>
                <p class="mt-4 text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('To exercise these rights, Users can send a request to:') }}
                    <a href="mailto:{{ config('company.email') }}"
                        class="text-blue-600 dark:text-blue-400 hover:underline">{{ config('company.email') }}</a>.
                </p>
            </section>

            {{-- Retention --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-blue-500 rounded-full inline-block"></span>
                    {{ __('Retention Period') }}
                </h2>
                <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ __('Personal Data shall be processed and stored for as long as required by the purpose they have been collected for. Therefore: Personal Data collected for purposes related to the performance of a contract between the Owner and the User shall be retained until such contract has been fully performed. Personal Data collected for the purposes of the Owner\'s legitimate interests shall be retained as long as needed to fulfil such purposes.') }}
                </p>
            </section>

            {{-- Cookie Policy Link --}}
            <section class="bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">
                    {{ __('Cookie Policy') }}
                </h2>
                <p class="text-sm text-blue-800 dark:text-blue-200 mb-3">
                    {{ __('This Website uses Trackers. To learn more, the User may consult the Cookie Policy.') }}
                </p>
                <a href="{{ route('cookie_policy') }}"
                    class="inline-flex items-center gap-1 text-sm font-medium text-blue-700 dark:text-blue-300 hover:underline">
                    {{ __('Read the Cookie Policy') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </section>

        </div>
    </div>
</div>
