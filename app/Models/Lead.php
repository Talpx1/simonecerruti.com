<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LeadReadStatus;
use App\Models\Concerns\LogsAllDirtyChanges;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Lead extends Model {
    /** @use HasFactory<\Database\Factories\LeadFactory> */
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
     * @return Attribute<LeadReadStatus, never>
     */
    protected function readStatus(): Attribute {
        return Attribute::get(fn () => $this->read_at ? LeadReadStatus::READ : LeadReadStatus::UNREAD);
    }

    /**
     * @return Attribute<string, never>
     */
    protected function source(): Attribute {
        return Attribute::get(fn () => $this->leadable_type::getLeadSourceName());
    }
}
