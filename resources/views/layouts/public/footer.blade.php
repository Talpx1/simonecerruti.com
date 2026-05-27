<footer id="main-footer"
    class="mt-2 px-4 flex flex-col lg:flex-row gap-8 lg:gap-0 items-center justify-between py-4 border-t border-light/[.07]">
    <x-social-links variant="icons" />

    <div
        class="flex items-center gap-4 text-[10px] text-light/20 uppercase [&>a]:hover:text-light/70 [&>a]:transition-colors [&>a]:duration-500">
        <a target="_blank" rel="noopener" href="{{ route('privacy_policy') }}">{{ __('Privacy Policy') }}</a>
        <a target="_blank" rel="noopener" href="{{ route('cookie_policy') }}">{{ __('Cookie Policy') }}</a>
        <a target="_blank" rel="noopener"
            href="{{ route('terms_and_conditions') }}">{{ __('Terms and Conditions') }}</a>

        <button onclick="Livewire.dispatch('open-cookie-banner')" type="button"
            class="hover:text-light/70 transition-colors duration-500 uppercase">
            {{ __('Cookie preferences') }}
        </button>
    </div>

    <p class="text-[10px] text-light/20 tracking-widest uppercase">
        {{ __('VAT') }} {{ config('company.vat') }} — © {{ date('Y') }} — {{ __('All rights reserved.') }}
    </p>
</footer>
