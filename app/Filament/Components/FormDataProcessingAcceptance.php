<?php

declare(strict_types=1);

namespace App\Filament\Components;

use Filament\Forms\Components\Checkbox;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class FormDataProcessingAcceptance {
    public static function make(string $name = 'acceptance'): Checkbox {
        return Checkbox::make($name)
            ->id(uniqid(Str::snake($name).'_'))
            ->columnSpanFull()
            ->label(__('Data processing'))
            ->belowContent(function () {
                $privacy_policy_url = '#';
                $terms_and_conditions_url = '#';

                return new HtmlString(__("I agree to receive communications from :company_name. By checking this box, I agree to :company_name's :terms_of_use and :privacy_policy.", [
                    'company_name' => config()->string('company.name'),
                    'terms_of_use' => "<a target='_blank' href='{$terms_and_conditions_url}' class='underline underline-offset-2 decoration-2'>".__('Terms of use').'</a>',
                    'privacy_policy' => "<a target='_blank' href='{$privacy_policy_url}' class='underline underline-offset-2 decoration-2'>".__('Privacy policy').'</a>',
                ]));
            })
            ->required();
    }
}
