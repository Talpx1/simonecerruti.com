<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Cookie;
use Livewire\Attributes\On;
use Livewire\Component;

class CookieConsent extends Component {
    /*
    |--------------------------------------------------------------------------
    | Cookie names & durations
    |--------------------------------------------------------------------------
    */
    const COOKIE_NAME = 'cookie_consent';

    const COOKIE_DURATION = 60 * 24 * 180; // 180 days in minutes

    /*
    |--------------------------------------------------------------------------
    | Component state
    |--------------------------------------------------------------------------
    */

    /** Whether the banner is visible */
    public bool $show = false;

    /** Whether the preference panel (granular controls) is open */
    public bool $showPreferences = false;

    /** Consent categories */
    public bool $analytics = false;  // Microsoft Clarity

    public bool $functional = false;  // Koalenda external link tracking

    public bool $marketing = false;  // Newsletter / Mailchimp (hidden, future)

    // Necessary is always true – no property needed

    /*
    |--------------------------------------------------------------------------
    | Lifecycle
    |--------------------------------------------------------------------------
    */

    public function mount(): void {
        $existing = request()->cookie(self::COOKIE_NAME);

        if ($existing) {
            $prefs = json_decode($existing, true);

            // Valid saved consent found – apply and hide banner
            if (is_array($prefs) && isset($prefs['version'])) {
                $this->analytics = (bool) ($prefs['analytics'] ?? false);
                $this->functional = (bool) ($prefs['functional'] ?? false);
                $this->marketing = (bool) ($prefs['marketing'] ?? false);
                $this->show = false;

                return;
            }
        }

        // No valid consent yet – show banner
        $this->show = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    /** Accept all non-necessary categories */
    public function acceptAll(): void {
        $this->analytics = true;
        $this->functional = true;
        $this->marketing = true;

        $this->saveAndClose();
    }

    /** Reject all non-necessary categories */
    public function rejectAll(): void {
        $this->analytics = false;
        $this->functional = false;
        $this->marketing = false;

        $this->saveAndClose();
    }

    /** Save the granular preferences chosen by the user */
    public function savePreferences(): void {
        $this->saveAndClose();
    }

    /** Open the granular preference panel */
    public function openPreferences(): void {
        $this->showPreferences = true;
    }

    #[On('open-cookie-banner')]
    public function openBanner(): void {
        $this->show = true;
        $this->showPreferences = false;
    }

    /** Withdraw consent (called from Privacy / Cookie Policy page) */
    public function withdrawConsent(): void {
        $this->analytics = false;
        $this->functional = false;
        $this->marketing = false;
        $this->show = true;
        $this->showPreferences = false;

        // Expire the consent cookie immediately
        Cookie::queue(Cookie::forget(self::COOKIE_NAME));

        $this->dispatch('consent-updated', [
            'necessary' => true,
            'analytics' => false,
            'functional' => false,
            'marketing' => false,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Internal helpers
    |--------------------------------------------------------------------------
    */

    private function saveAndClose(): void {
        $payload = json_encode([
            'version' => 1,
            'timestamp' => now()->toIso8601String(),
            'necessary' => true,
            'analytics' => $this->analytics,
            'functional' => $this->functional,
            'marketing' => $this->marketing,
        ]);

        // Queue an HTTP-only cookie (not accessible via JS – stores the record)
        Cookie::queue(
            self::COOKIE_NAME,
            $payload,
            self::COOKIE_DURATION,
            '/',
            null,
            config('session.secure', false),
            true,   // httpOnly
            false,
            'Lax'
        );

        $this->show = false;
        $this->showPreferences = false;

        // Dispatch to JS so third-party scripts can be loaded/unloaded
        $this->dispatch('consent-updated', [
            'necessary' => true,
            'analytics' => $this->analytics,
            'functional' => $this->functional,
            'marketing' => $this->marketing,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render() {
        return view('layouts.public.cookie-consent');
    }
}
