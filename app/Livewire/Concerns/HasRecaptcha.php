<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

trait HasRecaptcha {
    #[Locked]
    public bool $has_recaptcha = true;

    #[Computed]
    public function getRecaptchaAction(): string {
        return $this->recaptcha_action ?? Str::snake(class_basename(__CLASS__));
    }

    private function verifyRecaptcha(string $token): void {
        if (! app()->isProduction()) {
            return;
        }

        $response = Http::asForm()->post(config()->string('services.recaptcha.verify_url'), [
            'secret' => config()->string('services.recaptcha.secret'),
            'response' => $token,
            'remoteip' => request()->ip(),
        ])->body();

        $body = \Safe\json_decode($response, true);

        $action = $this->getRecaptchaAction;

        abort_if(! isset($body['success']) || $body['success'] !== true, 400, __('Captcha verification failed. Try again.'));
        abort_if(! isset($body['action']) || $action != $body['action'], 400, __('Captcha verification failed. Try again.'));
        abort_if(! isset($body['score']), 400, __('Captcha verification failed. Try again.'));
        abort_if($body['score'] < ($this->captcha_min_score ?? 0.7), 400, __('Captcha verification failed. Try again.'));
    }
}
