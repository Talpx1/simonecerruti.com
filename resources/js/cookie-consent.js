/**
 * cookie-consent.js
 * ─────────────────
 * Preventive cookie blocking layer.
 *
 * How it works:
 * 1. On page load, reads the current consent state from the
 *    `cookie_consent` HTTP cookie (parsed to JS via a small
 *    inline script in the layout – see README).
 * 2. Loads or blocks each third-party script accordingly.
 * 3. Listens for the `consent-updated` Livewire event and
 *    reacts in real-time (loads newly accepted scripts,
 *    informs the user a reload may be needed to fully remove
 *    already-loaded scripts).
 *
 * Script loading strategy:
 * - Scripts are injected dynamically only after consent is confirmed.
 * - Scripts that were blocked will be loaded immediately when consent
 *   is granted within the same session (no reload needed to start).
 * - Scripts that were already loaded cannot be "unloaded" from the DOM;
 *   a page reload is the clean solution when revoking consent.
 */

(function () {
    'use strict';

    // ─── State ─────────────────────────────────────────────────────────────

    /** Populated from the inline bootstrap in the layout (see README). */
    let _consent = window.__cookieConsent || {
        given:      false,
        necessary:  true,
        analytics:  false,
        functional: false,
        marketing:  false,
    };

    /** Tracks which scripts have already been injected this session. */
    const _loaded = {
        clarity:  false,
        recaptcha: false, // reCAPTCHA runs on legitimate interest – loaded always
    };

    // ─── Script definitions ─────────────────────────────────────────────────

    const log = (msg, severity = 'info') => {
        if (window.__appEnv === 'local') {
            console[severity]('[CookieConsent]', msg);
        }
    };

    /**
     * Inject Microsoft Clarity.
     */
    function loadClarity() {
        if (_loaded.clarity) return;
        _loaded.clarity = true;

        const projectId = window._clarityId;

        if(!projectId){
            log("Missing PROJECT ID for Microsoft Clarity", 'error');
            return;
        }

        (function (c, l, a, r, i, t, y) {
            c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments); };
            t = l.createElement(r);
            t.async = 1;
            t.src   = 'https://www.clarity.ms/tag/' + i;
            y = l.getElementsByTagName(r)[0];
            y.parentNode.insertBefore(t, y);
        })(window, document, 'clarity', 'script', projectId);

        log('Microsoft Clarity loaded.');
    }

    /**
     * reCAPTCHA – loaded on legitimate interest (no consent needed).
     * Still split out so it can be toggled if policy changes.
     */
    function loadRecaptcha() {
        if (_loaded.recaptcha) return;
        _loaded.recaptcha = true;
        // reCAPTCHA is typically loaded via the Blade partial below;
        // this function is a hook for dynamic forms loaded after page load.
        log('reCAPTCHA eligible (legitimate interest).');
    }

    // ─── Boot ────────────────────────────────────────────────────────────────

    function boot(consent) {
        // Analytics
        if (consent.analytics) {
            loadClarity();
        }

        // Functional (Koalenda external link – nothing to load server-side,
        // but we store consent so the link can append UTM params if desired)
        if (consent.functional) {
            document.documentElement.dataset.functionalConsent = 'true';
        } else {
            delete document.documentElement.dataset.functionalConsent;
        }

        // Marketing (hidden / future)
        if (consent.marketing) {
            document.documentElement.dataset.marketingConsent = 'true';
        } else {
            delete document.documentElement.dataset.marketingConsent;
        }

        // reCAPTCHA – always (legitimate interest)
        loadRecaptcha();
    }

    // ─── Livewire event listener ─────────────────────────────────────────────

    /**
     * Livewire dispatches `consent-updated` after every save/reject.
     * Works with Livewire v3's `@this.on` / `Livewire.on` syntax.
     */
    function registerLivewireListener() {
        document.addEventListener('consent-updated', function (e) {
            const consent = e.detail?.[0] ?? e.detail ?? {};
            _consent = Object.assign(_consent, consent);

            boot(_consent);

            // If the user REVOKED a consent that was already active,
            // the only clean way to stop the script is a page reload.
            const analyticsRevoked = !consent.analytics && _loaded.clarity;

            if (analyticsRevoked) {
                log('Consent revoked. Reload recommended to stop active trackers.');
                window.location.reload();
            }
        });
    }

    // ─── Init ────────────────────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', function () {
        boot(_consent);
        registerLivewireListener();
    });

    // Also re-run after Livewire navigations (if using wire:navigate)
    document.addEventListener('livewire:navigated', function () {
        boot(_consent);
    });

})();
