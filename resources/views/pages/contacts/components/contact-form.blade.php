<div>
    <form @submit.prevent="formSubmit" x-data="{
        recaptchaKey: @js(config('services.recaptcha.key')),
        recaptchaAction: @js($this->getRecaptchaAction ?? ''),

        async formSubmit() {
            const formParams = {}

            @if ($this->has_recaptcha ?? false) formParams.recaptcha_token = await grecaptcha.execute(this.recaptchaKey, {action: this.recaptchaAction}) @endif

            Livewire.dispatch(
                'formSubmitted.{{ $this->getName() }}', { form_params: formParams }
            );
        },
    }" class="mt-8 flex flex-col xl:flex-row gap-4">
        <div class="grow">
            {{ $this->form }}
        </div>

        <x-filament::button type="submit"
            class="w-full xl:w-fit text-dark bg-light hover:opacity-60 transition-all duration-500 mt-4 rounded-none uppercase font-black text-2xl">
            {{ __('Let\'s talk') }} 🡒
        </x-filament::button>

    </form>

    <x-filament-actions::modals />
</div>
