<div @if ($this->has_recaptcha ?? false) data-recaptcha-required @endif>
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
    }" class="flex flex-col gap-7">
        <div class="contact-fields">
            {{ $this->form }}
        </div>

        <div class="flex flex-wrap items-center gap-x-6 gap-y-4">
            <x-button as="button" type="submit" data-pan="cta-contacts-submit">{{ __('Let\'s talk') }}</x-button>
            <span class="text-xs uppercase tracking-[0.18em] text-light/40">{{ __('Reply within 24 hours') }}</span>
        </div>
    </form>

    <x-filament-actions::modals />
</div>
