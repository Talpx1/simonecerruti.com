<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasLead;
use App\Models\Concerns\LogsAllDirtyChanges;
use Carbon\CarbonImmutable;
use Database\Factories\ContactLeadFactory;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $company_name
 * @property string $email
 * @property string|null $phone
 * @property string $message
 * @property bool $acceptance
 * @property string|null $ip
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read non-falsy-string $full_name
 * @property-read string $lead_title
 * @property-read Lead|null $lead
 */
class ContactLead extends Model {
    /** @use HasFactory<ContactLeadFactory> */
    use HasFactory, HasLead, LogsAllDirtyChanges;

    protected function casts(): array {
        return [
            'acceptance' => 'boolean',
        ];
    }

    /** @return Attribute<non-falsy-string, never> */
    protected function fullName(): Attribute {
        return Attribute::get(fn () => "{$this->first_name} {$this->last_name}".($this->company_name ? " ({$this->company_name})" : ''));
    }

    /** @return Attribute<string, never> */
    protected function leadTitle(): Attribute {
        return Attribute::get(function (): string {
            $title = __('Contact request from :full_name', ['full_name' => $this->full_name]);

            return is_string($title) ? $title : '';
        });
    }

    public function getInfolistComponents(): array {
        return [
            TextEntry::make('first_name')->label(__('First name')),
            TextEntry::make('last_name')->label(__('Last name')),
            TextEntry::make('company_name')->label(__('Company name')),
            TextEntry::make('email')->label(__('Email')),
            TextEntry::make('phone')->label(__('Phone')),
            TextEntry::make('message')->label(__('Message'))->columnSpanFull(),
            IconEntry::make('acceptance')->label(__('Data processing accepted'))->boolean(),
            TextEntry::make('ip')->label(__('IP address')),
        ];
    }

    public static function getLeadSourceName(): string {
        $label = __('Contact Form');

        return is_string($label) ? $label : 'Contact Form';
    }
}
