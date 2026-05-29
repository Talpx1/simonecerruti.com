<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\PageViewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $visit_session_id
 * @property string $url_path
 * @property string|null $route_name
 * @property string $locale
 * @property CarbonImmutable $viewed_at
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read VisitSession $visitSession
 */
class PageView extends Model {
    /** @use HasFactory<PageViewFactory> */
    use HasFactory;

    protected function casts(): array {
        return [
            'viewed_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<VisitSession, $this> */
    public function visitSession(): BelongsTo {
        return $this->belongsTo(VisitSession::class);
    }
}
