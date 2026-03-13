<footer
    class="px-4 flex flex-col lg:flex-row gap-8 lg:gap-0 items-center justify-between py-4 border-t border-light/[.07]">
    <div class="flex items-center gap-4">
        <a href="{{ config('company.socials.linkedin.link') }}" target="_blank" rel="noopener"
            class="opacity-30 hover:opacity-100 text-light" title="LinkedIn">
            <x-fab-linkedin class="w-4" />
        </a>
        <a href="{{ config('company.socials.github.link') }}" target="_blank" rel="noopener"
            class="opacity-30 hover:opacity-100 text-light" title="GitHub">
            <x-fab-github class="w-4" />
        </a>
        <a href="{{ config('company.socials.instagram.link') }}" target="_blank" rel="noopener"
            class="opacity-30 hover:opacity-100 text-light" title="Instagram">
            <x-fab-instagram class="w-4" />
        </a>
        <a href="{{ config('company.socials.x.link') }}" target="_blank" rel="noopener"
            class="opacity-30 hover:opacity-100 text-light" title="X / Twitter">
            <x-fab-x-twitter class="w-4" />
        </a>
        <a href="{{ config('company.socials.bluesky.link') }}" target="_blank" rel="noopener"
            class="opacity-30 hover:opacity-100 text-light" title="BlueSky">
            <x-fab-bluesky class="w-4" />
        </a>
    </div>

    <div
        class="flex items-center gap-4 text-[10px] text-light/20 uppercase [&>a]:hover:text-light/70 [&>a]:transition-colors [&>a]:duration-500">
        <a target="_blank" rel="noopener" href="{{ route('privacy_policy') }}">{{ __('Privacy Policy') }}</a>
        <a target="_blank" rel="noopener" href="{{ route('cookie_policy') }}">{{ __('Cookie Policy') }}</a>
        <a target="_blank" rel="noopener"
            href="{{ route('terms_and_conditions') }}">{{ __('Terms and Conditions') }}</a>
    </div>

    <p class="text-[10px] text-light/20 tracking-widest uppercase">
        © {{ date('Y') }} — {{ __('All rights reserved.') }}
    </p>
</footer>
