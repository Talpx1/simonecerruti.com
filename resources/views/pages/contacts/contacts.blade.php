<div class="min-h-full h-full lg:max-h-full flex flex-col justify-between gap-16 xl:gap-8 2xl:gap-16 px-4 py-8 md:px-16 2xl:py-16"
    id="contacts">
    <div>
        <h1 class="text-center lg:text-left text-6xl lg:text-7xl 2xl:text-8xl font-black uppercase">{{ __('Contacts') }}
        </h1>
        <h2 class="text-3xl 2xl:text-5xl font-semibold text-center lg:text-left mt-2 lg:mt-4">
            {{ __('It starts here. The rest, we build together.') }}
        </h2>
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
            <a href="{{ config()->string('company.contacts.koalenda_url') }}" target="_blank" rel="noopener"
                class="text-dark bg-light hover:opacity-60 transition-all duration-500 mt-4 rounded-none uppercase p-4 w-full block">
                <h2 class="justify-center text-xl lg:text-2xl 2xl:text-4xl font-black flex items-center gap-1">
                    {{ __('Book a call') }} <x-ri-arrow-right-long-line class="w-6 lg:w-10" />
                </h2>
            </a>
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
                <h2 class="text-xl lg:text-2xl 2xl:text-4xl font-semibold">{{ __('Text me on WhatsApp') }}</h2>
            </div>
            <a target="_blank"
                href="{{ sprintf(
                    'https://wa.me/%s?text=%s',
                    config()->string('company.contacts.whatsapp.number'),
                    urlencode(__(config()->string('company.contacts.whatsapp.default_message'))),
                ) }}"
                class="justify-center text-center text-xl lg:text-2xl 2xl:text-4xl font-black text-dark bg-light hover:opacity-60 transition-all duration-500 mt-4 rounded-none uppercase p-4 flex items-center gap-1">
                {{ __('Start a Chat') }} <x-ri-arrow-right-long-line class="w-6 lg:w-10" />
            </a>
        </div>
    </div>

    <x-social-links variant="detailed" placement="contacts" />
</div>

@push('scripts')
@endpush
