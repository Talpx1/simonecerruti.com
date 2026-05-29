<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DeviceType;
use Carbon\CarbonImmutable;
use Database\Factories\VisitSessionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string|null $visitor_id
 * @property string $source
 * @property string|null $medium
 * @property int|null $campaign_id
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string|null $utm_term
 * @property string|null $utm_content
 * @property string|null $referrer_url
 * @property string|null $referrer_host
 * @property string $landing_path
 * @property string|null $landing_route_name
 * @property string $locale
 * @property string|null $ip
 * @property string|null $user_agent
 * @property DeviceType|null $device_type
 * @property string|null $country
 * @property bool $consent_analytics
 * @property CarbonImmutable $started_at
 * @property CarbonImmutable $last_activity_at
 * @property int $pageview_count
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read Campaign|null $campaign
 */
class VisitSession extends Model {
    /** @use HasFactory<VisitSessionFactory> */
    use HasFactory, HasUuids;

    protected function casts(): array {
        return [
            'device_type' => DeviceType::class,
            'started_at' => 'immutable_datetime',
            'last_activity_at' => 'immutable_datetime',
            'consent_analytics' => 'boolean',
            'pageview_count' => 'integer',
        ];
    }

    /** @return BelongsTo<Campaign, $this> */
    public function campaign(): BelongsTo {
        return $this->belongsTo(Campaign::class);
    }

    /** @return HasMany<PageView, $this> */
    public function pageViews(): HasMany {
        return $this->hasMany(PageView::class);
    }

    /** @param Builder<self> $query */
    protected function scopeActiveWindow(Builder $query): void {
        $window_minutes = config()->integer('analytics.session_window_minutes');

        $query->where('last_activity_at', '>=', now()->subMinutes($window_minutes));
    }

    /** @param Builder<self> $query */
    protected function scopeBySource(Builder $query, string $source): void {
        $query->where('source', $source);
    }

    /** @param Builder<self> $query */
    protected function scopeWithConsent(Builder $query): void {
        $query->where('consent_analytics', true);
    }
}
