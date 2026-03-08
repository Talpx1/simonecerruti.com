<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasLead;
use App\Models\Concerns\LogsAllDirtyChanges;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactLead extends Model {
    /** @use HasFactory<\Database\Factories\ContactLeadFactory> */
    use HasFactory, HasLead, LogsAllDirtyChanges;

    protected function casts(): array {
        return [
            'acceptance' => 'boolean',
        ];
    }

    /** @return Attribute<non-falsy-string, never> */
    public function fullName(): Attribute {
        return Attribute::get(fn () => "{$this->first_name} {$this->last_name}".($this->company_name ? " ({$this->company_name})" : ''));
    }

    protected function leadTitle(): Attribute {
        return Attribute::get(fn () => __('Contact request from :full_name', ['full_name' => $this->full_name]));
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
        return __('Contact Form');
    }
}
