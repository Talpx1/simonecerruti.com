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
    }" class="flex flex-col gap-8">
        <div class="contact-fields">
            {{ $this->form }}
        </div>

        <div>
            <button class="button-primary button-large magnetic-hover" type="submit" data-cursor="{{ __('Let\'s talk') }}" data-pan="cta-contacts-submit"><span>{{ __('Let\'s talk') }}</span><span class="button-arrow">→</span></button>
        </div>
    </form>

    <x-filament-actions::modals />
</div>
