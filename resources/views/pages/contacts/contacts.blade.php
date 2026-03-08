<div class="min-h-full h-full lg:max-h-full flex flex-col justify-between gap-16 xl:gap-8 2xl:gap-16 px-4 py-8 md:px-16 2xl:py-16"
    id="contacts">
    <div>
        <h1 class="text-center lg:text-left text-6xl lg:text-7xl 2xl:text-8xl font-black uppercase">{{ __('Contacts') }}
        </h1>
        <h2 class="text-3xl 2xl:text-5xl">{{ __('It starts here. The rest, we build together.') }}</h2>
        <livewire:components.contact-form />
    </div>

    <div class="flex flex-col md:flex-row justify-between xl:grid xl:grid-cols-3 border-light border-y py-8 xl:py-0">
        <div class="px-0 2xl:px-8 py-4 2xl:py-16 md:grid md:place-content-center">
            <div class="flex gap-2 items-center">
                <x-heroicon-o-phone class="w-7" />
                <p class="text-xl lg:text-2xl 2xl:text-4xl font-semibold">

                    {{ __('Prefer talking on writing?') }}
                </p>
            </div>
            <button
                class="text-dark bg-light hover:opacity-60 transition-all duration-500 mt-4 rounded-none uppercase p-4 w-full">
                <h2 class="text-xl lg:text-2xl 2xl:text-4xl font-black">
                    {{ __('Book a call') }} 🡒
                </h2>
            </button>
        </div>

        <div class="px-0 2xl:px-8 py-4 2xl:py-16 xl:border-x xl:border-light md:grid md:place-content-center">
            <div class="flex gap-2 items-center">
                <x-heroicon-o-envelope class="w-7" />
                <h2 class="text-xl lg:text-2xl 2xl:text-4xl font-semibold">{{ __('I also have an email') }}</h2>
            </div>
            <a href="mailto:{{ config('company.contacts.email') }}"
                class="block text-dark bg-light hover:opacity-60 transition-all duration-500 mt-4 rounded-none uppercase p-4 underline decoration-4 underline-offset-4">
                <h3 class="text-lg md:text-xl lg:text-xl 2xl:text-4xl font-black">
                    {{ config('company.contacts.email') }}
                </h3>
            </a>
        </div>

        <div class="px-0 2xl:px-8 py-4 2xl:py-16 md:grid md:place-content-center">
            <div class="flex gap-2 items-center">
                <x-fab-whatsapp class="w-7" />
                <h2 class="text-xl lg:text-2xl 2xl:text-4xl font-semibold">{{ __('Scrivimi su WhatsApp') }}</h2>
            </div>
            <a target="_blank"
                href="{{ sprintf(
                    'https://wa.me/%s?text=%s',
                    config()->string('company.contacts.whatsapp.number'),
                    urlencode(__(config()->string('company.contacts.whatsapp.default_message'))),
                ) }}"
                class="block text-center text-xl lg:text-2xl 2xl:text-4xl font-black text-dark bg-light hover:opacity-60 transition-all duration-500 mt-4 rounded-none uppercase p-4">
                {{ __('Start a Chat') }} 🡒
            </a>
        </div>
    </div>

    <div
        class="flex flex-col lg:flex-row w-fit lg:w-full md:w-auto mx-auto md:flex-row gap-8 md:gap-0 md:justify-between items-start md:items-center">
        <div>
            <a target="_blank" rel="noopener" href="{{ config('company.socials.linkedin.link') }}">
                <div class="flex gap-2 items-center">
                    <x-fab-linkedin class="w-7" />
                    <h2 class="text-xl font-semibold underline">{{ config('company.socials.linkedin.username') }}</h2>
                    <x-fas-external-link-alt class="w-3 self-start" />
                </div>
                <h3 class="mt-1">LinkedIn</h3>
            </a>
        </div>
        <div>
            <a target="_blank" rel="noopener" href="{{ config('company.socials.instagram.link') }}">
                <div class="flex gap-2 items-center">
                    <x-fab-instagram class="w-7" />
                    <h2 class="text-xl font-semibold underline">{{ config('company.socials.instagram.username') }}</h2>
                    <x-fas-external-link-alt class="w-3 self-start" />
                </div>
                <h3 class="mt-1">Instagram</h3>
            </a>
        </div>
        <div>
            <a target="_blank" rel="noopener" href="{{ config('company.socials.github.link') }}">
                <div class="flex gap-2 items-center">
                    <x-fab-github class="w-7" />
                    <h2 class="text-xl font-semibold underline">{{ config('company.socials.github.username') }}</h2>
                    <x-fas-external-link-alt class="w-3 self-start" />
                </div>
                <h3 class="mt-1">GitHub</h3>
            </a>
        </div>
        <div>
            <a target="_blank" rel="noopener" href="{{ config('company.socials.bluesky.link') }}">
                <div class="flex gap-2 items-center">
                    <x-fab-bluesky class="w-7" />
                    <h2 class="text-xl font-semibold underline">{{ config('company.socials.bluesky.username') }}</h2>
                    <x-fas-external-link-alt class="w-3 self-start" />
                </div>
                <h3 class="mt-1">BlueSky</h3>
            </a>
        </div>
        <div>
            <a target="_blank" rel="noopener" href="{{ config('company.socials.x.link') }}">
                <div class="flex gap-2 items-center">
                    <x-fab-x-twitter class="w-7" />
                    <h2 class="text-xl font-semibold underline">{{ config('company.socials.x.username') }}</h2>
                    <x-fas-external-link-alt class="w-3 self-start" />
                </div>
                <h3 class="mt-1">X/Twitter</h3>
            </a>
        </div>
    </div>
</div>

@push('scripts')
@endpush
