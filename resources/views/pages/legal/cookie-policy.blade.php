<div>
    <section class="legal-hero">
        <h1 class="legal-h1">{{ __('Cookie Policy') }}<span class="acc">.</span></h1>

        <div class="legal-meta">
            <p>{{ __('Which cookies this website uses, what they are for and how you can manage them.') }}</p>
            <span>{{ __('Last updated:') }} <time>13/03/2026</time></span>
        </div>

        <x-legal-switcher current="cookies" />
    </section>

    <div class="art-grid legal-grid" data-fx="legal">
        <aside class="art-side" aria-label="{{ __('Index') }}">
            <div class="art-prog" aria-hidden="true"><i></i></div>
            <nav class="toc" aria-label="{{ __('Index') }}">
                <a href="#what-are-cookies-and-similar-technologie">{{ __('What are Cookies and similar technologies?') }}</a>
                <a href="#how-this-website-uses-cookies-and-tracke">{{ __('How this Website uses Cookies and Trackers') }}</a>
                <a href="#cookie-categories">{{ __('Cookie Categories') }}</a>
                <a href="#how-to-manage-cookies">{{ __('How to manage Cookies') }}</a>
                <a href="#data-controller">{{ __('Data Controller') }}</a>
            </nav>
        </aside>

        <article class="art-body legal-body">

        <section>
            <p>
                {{ __('This document informs Users about the technologies that help this Website to achieve the purposes described below. Such technologies allow the Owner to access and store information (for example by using a Cookie) or use resources (for example by running a script) on a User\'s device as they interact with this Website.') }}
            </p>
        </section>

        <section>
            <h2 id="what-are-cookies-and-similar-technologie">
                {{ __('What are Cookies and similar technologies?') }}
            </h2>
            <p>
                {{ __('Cookies are small files stored on your device. They serve many functions: they can help this Website remember your preferences, understand how you use the Website, and serve you relevant information. In addition to Cookies, this Website may use other technologies such as web beacons, pixel tags, localStorage, and sessionStorage.') }}
            </p>
            <div>
                <div>
                    <h3>{{ __('Session Cookies') }}
                    </h3>
                    <p>
                        {{ __('Temporary cookies that expire when you close your browser. They do not collect information from your computer.') }}
                    </p>
                </div>
                <div>
                    <h3>
                        {{ __('Persistent Cookies') }}</h3>
                    <p>
                        {{ __('Remain on your device for a set period. They are activated each time you visit the Website that created them.') }}
                    </p>
                </div>
                <div>
                    <h3>
                        {{ __('First-party Cookies') }}</h3>
                    <p>
                        {{ __('Set directly by this Website for its own purposes.') }}</p>
                </div>
                <div>
                    <h3>
                        {{ __('Third-party Cookies') }}</h3>
                    <p>
                        {{ __('Set by external services used by this Website (e.g. analytics, embedded content).') }}
                    </p>
                </div>
            </div>
        </section>

        <section>
            <h2 id="how-this-website-uses-cookies-and-tracke">
                {{ __('How this Website uses Cookies and Trackers') }}
            </h2>

            <div class="legal-table">
                <table>
                    <thead>
                        <tr>
                            <th>
                                {{ __('Tracker / Service') }}</th>
                            <th>
                                {{ __('Type') }}</th>
                            <th>
                                {{ __('Purpose') }}</th>
                            <th>
                                {{ __('Consent required?') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Laravel Session</td>
                            <td>
                                {{ __('Session Cookie (1st party)') }}</td>
                            <td>
                                {{ __('Maintains user session, CSRF protection, flash messages.') }}</td>
                            <td><span>{{ __('No – technical') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>vs_id</td>
                            <td>
                                {{ __('Session Cookie (1st party, 30 minutes)') }}</td>
                            <td>
                                {{ __('Anonymous technical session for internal traffic statistics. No personal data.') }}</td>
                            <td><span>{{ __('No – technical') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>v_id</td>
                            <td>
                                {{ __('Persistent Cookie (1st party, 1 year)') }}</td>
                            <td>
                                {{ __('Recognises returning visitors for aggregated traffic statistics.') }}</td>
                            <td><span>{{ __('Yes – analytics') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Font Bunny</td>
                            <td>
                                {{ __('External resource (EU)') }}</td>
                            <td>
                                {{ __('Loads web fonts from Bunny.net CDN without tracking.') }}</td>
                            <td><span>{{ __('No – no personal data') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Microsoft Clarity</td>
                            <td>
                                {{ __('Analytics (3rd party)') }}</td>
                            <td>
                                {{ __('Heatmaps, session recordings, behavioural analytics.') }}</td>
                            <td><span>{{ __('Yes – analytics') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Google reCAPTCHA</td>
                            <td>
                                {{ __('Anti-spam (3rd party)') }}</td>
                            <td>
                                {{ __('Protects forms from bots and automated submissions.') }}</td>
                            <td><span>{{ __('No – legitimate interest') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Koalenda</td>
                            <td>
                                {{ __('External link (3rd party)') }}</td>
                            <td>
                                {{ __('Redirects to Koalenda\'s external platform. No cookies set by this Website.') }}
                            </td>
                            <td><span>{{ __('No – external redirect') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>sessionStorage</td>
                            <td>
                                {{ __('Browser storage (1st party)') }}</td>
                            <td>
                                {{ __('Temporary in-tab storage. Cleared on tab close.') }}</td>
                            <td><span>{{ __('No – technical') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>localStorage</td>
                            <td>
                                {{ __('Browser storage (1st party)') }}</td>
                            <td>
                                {{ __('Persistent local storage for preferences (e.g. theme, language).') }}</td>
                            <td><span>{{ __('No – preferences') }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2 id="cookie-categories">
                {{ __('Cookie Categories') }}
            </h2>
            <div>
                <div>
                    <h3>{{ __('Strictly Necessary') }}
                    </h3>
                    <p>
                        {{ __('These cookies are essential for the Website to function correctly and cannot be switched off. They are usually set in response to actions you take, such as setting your privacy preferences, logging in, or filling in forms. These cookies do not store personally identifiable information.') }}
                    </p>
                </div>
                <div>
                    <h3>
                        {{ __('Functional / Preference') }}</h3>
                    <p>
                        {{ __('These cookies enable enhanced functionality and personalisation. They may be set by us or by third-party providers whose services we have added to our pages. If you do not allow these cookies, some or all of these services may not function properly.') }}
                    </p>
                </div>
                <div>
                    <h3>
                        {{ __('Analytics / Performance') }}</h3>
                    <p>
                        {{ __('These cookies (e.g. Microsoft Clarity) allow us to count visits and traffic sources so we can measure and improve the performance of our Website. They help us know which pages are the most and least popular. All information these cookies collect is aggregated.') }}
                    </p>
                </div>
            </div>
        </section>

        <section>
            <h2 id="how-to-manage-cookies">
                {{ __('How to manage Cookies') }}
            </h2>
            <p>
                {{ __('Users can set or amend their web browser controls to accept or refuse cookies. If a User chooses to reject cookies, they may still use our Website though their access to some functionality may be restricted. As the means by which a User can refuse cookies through their web browser controls vary from browser-to-browser, Users should visit their browser\'s help menu for more information.') }}
            </p>
            <div>
                @foreach ([['Chrome', 'https://support.google.com/chrome/answer/95647'], ['Firefox', 'https://support.mozilla.org/en-US/kb/enhanced-tracking-protection-firefox-desktop'], ['Safari', 'https://support.apple.com/guide/safari/manage-cookies-sfri11471'], ['Edge', 'https://support.microsoft.com/en-us/windows/manage-cookies-in-microsoft-edge']] as [$browser, $url])
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="legal-chip">
                        {{ $browser }}

                    </a>
                @endforeach
            </div>
            <button onclick="Livewire.dispatch('open-cookie-banner')" type="button">
                {{ __('Cookie preferences') }}
            </button>
        </section>

        <section>
            <h2 id="data-controller">
                {{ __('Data Controller') }}
            </h2>
            <p>
                {{ config('company.name') }} – {{ config('company.vat') }}<br>
                <a href="mailto:{{ config('company.contacts.email') }}">{{ config('company.contacts.email') }}</a>
            </p>
        </section>
        </article>
    </div>
</div>
