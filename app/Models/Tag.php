<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ServiceAreas;
use Carbon\CarbonImmutable;
use Spatie\Tags\Tag as BaseTag;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property ServiceAreas|null $service
 * @property string|null $type
 * @property int|null $order_column
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
class Tag extends BaseTag {
    /** @var list<string> */
    public array $translatable = ['name', 'slug', 'description'];

    protected function casts(): array {
        return [
            'service' => ServiceAreas::class,
        ];
    }
}
