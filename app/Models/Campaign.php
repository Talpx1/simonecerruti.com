<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VisitMediumType;
use Carbon\CarbonImmutable;
use Database\Factories\CampaignFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Tags\HasTags;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $source
 * @property VisitMediumType|null $medium
 * @property string|null $description
 * @property CarbonImmutable|null $starts_at
 * @property CarbonImmutable|null $ends_at
 * @property string|null $notes
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 */
class Campaign extends Model {
    /** @use HasFactory<CampaignFactory> */
    use HasFactory, HasTags;

    protected function casts(): array {
        return [
            'medium' => VisitMediumType::class,
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
        ];
    }

    /** @param Builder<self> $query */
    protected function scopeActive(Builder $query): void {
        $now = now();

        $query
            ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn (Builder $q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    /** @return HasMany<VisitSession, $this> */
    public function visitSessions(): HasMany {
        return $this->hasMany(VisitSession::class);
    }
}
