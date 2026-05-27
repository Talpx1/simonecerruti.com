<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LeadReadStatus;
use App\Models\Concerns\LogsAllDirtyChanges;
use Carbon\CarbonImmutable;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property class-string<ContactLead> $leadable_type
 * @property int $leadable_id
 * @property CarbonImmutable|null $read_at
 * @property bool $is_pinned
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read LeadReadStatus $read_status
 * @property-read string $source
 * @property-read ContactLead $leadable
 */
class Lead extends Model {
    /** @use HasFactory<LeadFactory> */
    use HasFactory, LogsAllDirtyChanges;

    protected function casts(): array {
        return [
            'read_at' => 'immutable_datetime',
            'is_pinned' => 'boolean',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function leadable(): MorphTo {
        return $this->morphTo('leadable');
    }

    /**
     * @return Attribute<LeadReadStatus::READ|LeadReadStatus::UNREAD, never>
     */
    protected function readStatus(): Attribute {
        return Attribute::get(fn () => $this->read_at ? LeadReadStatus::READ : LeadReadStatus::UNREAD);
    }

    /**
     * @return Attribute<string, never>
     */
    protected function source(): Attribute {
        return Attribute::get(fn (): string => $this->leadable_type::getLeadSourceName());
    }
}
