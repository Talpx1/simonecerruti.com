<div>
    <section class="legal-hero">
        <h1 class="legal-h1">{{ __('Privacy Policy') }}<span class="text-accent">.</span></h1>

        <div class="legal-meta">
            <p>{{ __('Which data this website collects, why, for how long and what your rights are.') }}</p>
            <span>{{ __('Last updated:') }} <time>13/03/2026</time></span>
        </div>

        <x-legal-switcher current="privacy" />
    </section>

    <div class="article-layout legal-grid" data-effect="legal">
        <aside class="article-sidebar" aria-label="{{ __('Index') }}">
            <div class="article-progress" aria-hidden="true"><i></i></div>
            <nav class="article-toc" aria-label="{{ __('Index') }}">
                <a href="#data-controller-owner">{{ __('Data Controller (Owner)') }}</a>
                <a href="#types-of-data-collected">{{ __('Types of Data Collected') }}</a>
                <a href="#place-of-processing">{{ __('Place of Processing') }}</a>
                <a href="#purposes-of-processing-and-legal-basis">{{ __('Purposes of Processing and Legal Basis') }}</a>
                <a href="#rights-of-users">{{ __('Rights of Users') }}</a>
                <a href="#retention-period">{{ __('Retention Period') }}</a>
                <a href="#cookie-policy">{{ __('Cookie Policy') }}</a>
            </nav>
        </aside>

        <article class="article-body legal-body">

        <section>
            <p>
                {{ __('This document informs Users about the technologies that help this Website to achieve the purposes described below. Such technologies allow the Owner to access and store information (for example by using a Cookie) or use resources (for example by running a script) on a User\'s device as they interact with this Website.') }}
            </p>
            <p>
                {{ __('For simplicity, all such technologies are defined as "Trackers" within this document – unless there is a reason to differentiate. For example, while Cookies can be used on both web and mobile browsers, it would be inaccurate to talk about Cookies in the context of mobile apps as they are a browser-based Tracker. For this reason, within this document, the term Cookies is only used where it is specifically meant to indicate that particular type of Tracker.') }}
            </p>
        </section>

        <section>
            <h2 id="data-controller-owner">
                {{ __('Data Controller (Owner)') }}
            </h2>
            <div>
                <p>
                    <b>{{ __('Company Name:') }}</b>
                    {{ config('company.name') }}
                </p>
                <p>
                    <b>{{ __('VAT Number:') }}</b>
                    {{ config('company.vat') }}
                </p>

                <p>
                    <b>{{ __('Contact Email:') }}</b>
                    <a href="mailto:{{ config('company.contacts.email') }}">
                        {{ config('company.contacts.email') }}
                    </a>
                </p>
            </div>
        </section>

        <section>
            <h2 id="types-of-data-collected">
                {{ __('Types of Data Collected') }}
            </h2>
            <p>
                {{ __('Among the types of Personal Data that this Website collects, by itself or through third parties, there are: first name; last name; company name; email address; phone number; IP address; Usage Data (browser User Agent).') }}
            </p>
            <p>
                {{ __('Complete details on each type of Personal Data collected are provided in the dedicated sections of this privacy policy or by specific explanation texts displayed prior to the Data collection. Personal Data may be freely provided by the User, or, in case of Usage Data, collected automatically when using this Website.') }}
            </p>
        </section>

        <section>
            <h2 id="place-of-processing">
                {{ __('Place of Processing') }}
            </h2>
            <p>
                {{ __('The Data is processed at the Owner\'s operating offices and in any other places where the parties involved in the processing are located. The Website is hosted on servers provided by Hetzner Online GmbH (Germany/EU). Backups are stored on Google Drive.') }}
            </p>
        </section>

        <section>
            <h2 id="purposes-of-processing-and-legal-basis">
                {{ __('Purposes of Processing and Legal Basis') }}
            </h2>
            <div>

                <div>
                    <div>
                        <h3>
                            {{ __('Contact Form (Proprietary)') }}
                        </h3>
                    </div>
                    <div>
                        <p>{{ __('By filling in the contact form with their Data, the User authorises this Website to use such details to reply to requests for information, quotes, or any other kind of request as indicated by the form\'s header.') }}
                        </p>
                        <p><b>{{ __('Data collected:') }}</b>
                            {{ __('First name, last name, company name, email address, phone number, IP address, User Agent.') }}
                        </p>
                        <p><b>{{ __('Legal basis:') }}</b>
                            {{ __('Consent (Art. 6(1)(a) GDPR); legitimate interest in responding to enquiries.') }}
                        </p>
                    </div>
                </div>

                <div>
                    <div>
                        <h3>
                            {{ __('Font Bunny (Typography)') }}
                        </h3>
                    </div>
                    <div>
                        <p>{{ __('Font Bunny is a GDPR-compliant font hosting service. Web fonts are loaded from Font Bunny\'s servers. No personal data is transmitted to third parties outside the EU.') }}
                        </p>
                        <p><b>{{ __('Service provider:') }}</b> Bunny.net (EU)</p>
                        <p><b>{{ __('Privacy policy:') }}</b> <a
                                href="https://bunny.net/privacy" target="_blank" rel="noopener noreferrer">bunny.net/privacy</a></p>
                    </div>
                </div>

                <div>
                    <div>
                        <h3>
                            {{ __('Koalenda (Appointment Scheduling)') }}
                        </h3>
                    </div>
                    <div>
                        <p>{{ __('This Website contains a link to Koalenda\'s scheduling platform. By clicking the link, the User is redirected to an external website governed by Koalenda\'s own privacy policy. No data is transmitted to Koalenda unless the User actively uses their platform.') }}
                        </p>
                        <p><b>{{ __('Service provider:') }}</b> Koalenda</p>
                        <p><b>{{ __('Privacy policy:') }}</b> <a
                                href="https://koalenda.com/privacy-policy" target="_blank" rel="noopener noreferrer">koalenda.com/privacy-policy</a>
                        </p>
                    </div>
                </div>

                <div>
                    <div>
                        <h3>
                            {{ __('Microsoft Clarity (Analytics & Heatmaps)') }}
                        </h3>
                    </div>
                    <div>
                        <p>{{ __('Microsoft Clarity is a behavioural analytics tool that helps understand how Users interact with this Website via heatmaps, session recordings and click analytics. It may collect Usage Data including IP address and User Agent.') }}
                        </p>
                        <p><b>{{ __('Service provider:') }}</b> Microsoft Corporation
                            (USA)</p>
                        <p><b>{{ __('Privacy policy:') }}</b> <a
                                href="https://privacy.microsoft.com/privacystatement" target="_blank"
                                rel="noopener noreferrer">privacy.microsoft.com</a>
                        </p>
                        <p><b>{{ __('Legal basis:') }}</b>
                            {{ __('Consent (Art. 6(1)(a) GDPR).') }}</p>
                    </div>
                </div>

                <div>
                    <div>
                        <h3>
                            {{ __('StatusCake (Uptime Monitoring)') }}
                        </h3>
                    </div>
                    <div>
                        <p>{{ __('StatusCake is used to monitor the Website\'s availability. It makes periodic requests to this Website\'s URLs; logs may include IP addresses.') }}
                        </p>
                        <p><b>{{ __('Service provider:') }}</b> StatusCake (UK)</p>
                        <p><b>{{ __('Privacy policy:') }}</b> <a
                                href="https://www.statuscake.com/privacy-policy/" target="_blank"
                                rel="noopener noreferrer">statuscake.com/privacy-policy</a>
                        </p>
                    </div>
                </div>

                <div>
                    <div>
                        <h3>
                            {{ __('Hetzner (Hosting Infrastructure)') }}
                        </h3>
                    </div>
                    <div>
                        <p>{{ __('This Website is hosted on servers provided by Hetzner Online GmbH. As a hosting provider, Hetzner may process technical data including IP addresses in server access logs.') }}
                        </p>
                        <p><b>{{ __('Service provider:') }}</b> Hetzner Online GmbH,
                            Industriestr. 25, 91710 Gunzenhausen, Germany</p>
                        <p><b>{{ __('Privacy policy:') }}</b> <a
                                href="https://www.hetzner.com/legal/privacy-policy" target="_blank"
                                rel="noopener noreferrer">hetzner.com/legal/privacy-policy</a>
                        </p>
                    </div>
                </div>

                <div>
                    <div>
                        <h3>
                            {{ __('Google Drive (Backup Storage)') }}
                        </h3>
                    </div>
                    <div>
                        <p>{{ __('Database backups are stored on Google Drive. This means that data collected through this Website (including personal data submitted via forms) may be included in encrypted backup files stored on Google\'s infrastructure.') }}
                        </p>
                        <p><b>{{ __('Service provider:') }}</b> Google LLC (USA)</p>
                        <p><b>{{ __('Privacy policy:') }}</b> <a
                                href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">policies.google.com/privacy</a>
                        </p>
                    </div>
                </div>

                <div>
                    <div>
                        <h3>
                            {{ __('Google reCAPTCHA (Spam Protection)') }}
                        </h3>
                    </div>
                    <div>
                        <p>{{ __('Google reCAPTCHA is used to protect forms on this Website from spam and automated abuse. It collects hardware and software information (such as device and application data) and sends it to Google for analysis.') }}
                        </p>
                        <p><b>{{ __('Service provider:') }}</b> Google LLC (USA)</p>
                        <p><b>{{ __('Privacy policy:') }}</b> <a
                                href="https://policies.google.com/privacy" target="_blank"
                                rel="noopener noreferrer">policies.google.com/privacy</a>
                        </p>
                        <p><b>{{ __('Legal basis:') }}</b>
                            {{ __('Legitimate interest in protecting the Website from abusive automated requests (Art. 6(1)(f) GDPR).') }}
                        </p>
                    </div>
                </div>

                <div>
                    <div>
                        <h3>
                            {{ __('WhatsApp Chat Button') }}
                        </h3>
                    </div>
                    <div>
                        <p>{{ __('This Website includes a button to initiate a WhatsApp chat. Clicking the button will redirect the User to WhatsApp\'s platform. Any data shared in a WhatsApp conversation is processed by Meta Platforms, Inc. under WhatsApp\'s own privacy policy.') }}
                        </p>
                        <p><b>{{ __('Service provider:') }}</b> Meta Platforms, Inc.
                            (USA)</p>
                        <p><b>{{ __('Privacy policy:') }}</b> <a
                                href="https://www.whatsapp.com/legal/privacy-policy" target="_blank"
                                rel="noopener noreferrer">whatsapp.com/legal/privacy-policy</a>
                        </p>
                    </div>
                </div>

                <div>
                    <div>
                        <h3>
                            {{ __('Browser Storage (sessionStorage & localStorage)') }}
                        </h3>
                    </div>
                    <div>
                        <p>{{ __('This Website uses sessionStorage and localStorage to store data locally in the User\'s browser. sessionStorage data is deleted when the browser tab is closed; localStorage data persists until explicitly cleared. These mechanisms are used to improve Website functionality and User experience.') }}
                        </p>
                        <p><b>{{ __('Legal basis:') }}</b>
                            {{ __('Legitimate interest in providing technical functionality (Art. 6(1)(f) GDPR) and/or consent where required.') }}
                        </p>
                    </div>
                </div>

                

                

            </div>
        </section>

        <section>
            <h2 id="rights-of-users">
                {{ __('Rights of Users') }}
            </h2>
            <p>
                {{ __('Users may exercise certain rights regarding their Data processed by the Owner. In particular, Users have the right to:') }}
            </p>
            <ul>
                @foreach ([__('Withdraw their consent at any time.'), __('Object to processing of their Data.'), __('Access their Data.'), __('Verify and seek rectification.'), __('Restrict the processing of their Data.'), __('Have their Personal Data deleted or otherwise removed.'), __('Receive their Data and have it transferred to another controller.'), __('Lodge a complaint with the competent supervisory authority (Garante per la protezione dei dati personali – www.garanteprivacy.it).')] as $right)
                    <li>

                        {{ $right }}
                    </li>
                @endforeach
            </ul>
            <p>
                {{ __('To exercise these rights, Users can send a request to:') }}
                <a href="mailto:{{ config('company.contacts.email') }}">{{ config('company.contacts.email') }}</a>.
            </p>
        </section>

        <section>
            <h2 id="retention-period">
                {{ __('Retention Period') }}
            </h2>
            <p>
                {{ __('Personal Data shall be processed and stored for as long as required by the purpose they have been collected for. Therefore: Personal Data collected for purposes related to the performance of a contract between the Owner and the User shall be retained until such contract has been fully performed. Personal Data collected for the purposes of the Owner\'s legitimate interests shall be retained as long as needed to fulfil such purposes.') }}
            </p>
        </section>

        <section>
            <h2 id="cookie-policy">
                {{ __('Cookie Policy') }}
            </h2>
            <p>
                {{ __('This Website uses Trackers. To learn more, the User may consult the Cookie Policy.') }}
            </p>
            <a href="{{ route('cookie_policy') }}">
                {{ __('Read the Cookie Policy') }}

            </a>
        </section>
        </article>
    </div>
</div>
