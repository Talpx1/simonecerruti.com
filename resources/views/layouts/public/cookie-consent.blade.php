<div>
    @if ($show)
        <section @class(['ck', 'show', 'open' => $showPreferences]) role="dialog" aria-modal="false" aria-labelledby="ck-t"
            aria-live="polite"
            x-data
            x-init="if (!matchMedia('(prefers-reduced-motion: reduce)').matches) { $el.animate([{ transform: 'translateY(calc(100% + 40px))' }, { transform: 'none' }], { duration: 700, easing: 'cubic-bezier(.2,.8,.2,1)' }) }">
            <div class="ck-in">
                <svg class="ck-bis" viewBox="0 0 44 44" aria-hidden="true">
                    <circle class="b" cx="22" cy="22" r="19" />
                    <circle class="bite" cx="38" cy="8" r="9" />
                    <circle class="dot" cx="15" cy="17" r="2" />
                    <circle class="dot" cx="24" cy="27" r="2" />
                    <circle class="dot" cx="14" cy="29" r="1.6" />
                    <circle class="dot" cx="28" cy="16" r="1.4" />
                </svg>

                <div>
                    <h2 id="ck-t">{{ $showPreferences ? __('Manage cookie preferences') : __('We use cookies') }}</h2>
                    <p>
                        {{ __('We use cookies and similar technologies to improve your experience, analyse traffic and enable certain features. Some are strictly necessary; others require your consent.') }}
                        <a href="{{ route('cookie_policy') }}" wire:navigate>{{ __('Cookie Policy') }}</a>
                        ·
                        <a href="{{ route('privacy_policy') }}" wire:navigate>{{ __('Privacy Policy') }}</a>
                    </p>
                </div>

                @if ($showPreferences)
                    <div class="ck-prefs">
                        <div>
                            <div class="ck-row">
                                <div>
                                    <b>{{ __('Strictly necessary') }}</b>
                                    <span>{{ __('Session management, CSRF protection, consent record. Cannot be disabled.') }}</span>
                                </div>
                                <button type="button" class="ck-sw" role="switch" aria-checked="true" disabled
                                    aria-label="{{ __('Strictly necessary') }}"></button>
                            </div>

                            <div class="ck-row">
                                <div>
                                    <b>{{ __('Analytics') }}</b>
                                    <span>{{ __('Microsoft Clarity (heatmaps, session recordings) and internal traffic statistics (returning visitor recognition, device type, country). Aggregated, no advertising.') }}</span>
                                </div>
                                <x-layouts::public.cookie-toggle wire:model="analytics" id="toggle-analytics"
                                    :label="__('Analytics')" />
                            </div>

                            <div class="ck-row">
                                <div>
                                    <b>{{ __('Functional') }}</b>
                                    <span>{{ __('Koalenda – external scheduling link. Enables tracking of referral source on their platform.') }}</span>
                                </div>
                                <x-layouts::public.cookie-toggle wire:model="functional" id="toggle-functional"
                                    :label="__('Functional')" />
                            </div>

                            <div class="ck-row">
                                <div>
                                    <b>{{ __('Marketing') }} ({{ __('coming soon') }})</b>
                                    <span>{{ __('Newsletter & Mailchimp – email marketing communications. Not yet active.') }}</span>
                                </div>
                                <x-layouts::public.cookie-toggle wire:model="marketing" id="toggle-marketing"
                                    :label="__('Marketing')" />
                            </div>

                            <p class="ck-note">
                                {{ __('You can change your choices at any time from our') }}
                                <a href="{{ route('cookie_policy') }}" wire:navigate>{{ __('Cookie Policy') }}</a>.
                                {{ __('Refusing optional cookies will not affect basic functionality.') }}
                            </p>
                        </div>
                    </div>
                @endif

                <div class="ck-acts">
                    @if ($showPreferences)
                        <button type="button" class="ck-more" wire:click="$set('showPreferences', false)">{{ __('Back') }}</button>
                        <button type="button" class="ck-no" wire:click="rejectAll">{{ __('Reject all') }}</button>
                        <button type="button" class="ck-ok" wire:click="savePreferences">{{ __('Save my preferences') }}</button>
                    @else
                        <button type="button" class="ck-more" wire:click="openPreferences">{{ __('Manage preferences') }}</button>
                        <button type="button" class="ck-no" wire:click="rejectAll">{{ __('Reject all') }}</button>
                        <button type="button" class="ck-ok" wire:click="acceptAll">{{ __('Accept all') }}</button>
                    @endif
                </div>
            </div>
        </section>
    @endif
</div>
