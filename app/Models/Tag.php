<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\FillMissingTagTranslations;
use App\Enums\ServiceAreas;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
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

    /**
     * @param  string|array<int, string|\ArrayAccess<int, mixed>>|\ArrayAccess<int, mixed>  $values
     * @return Collection<int, BaseTag>|BaseTag|static
     */
    public static function findOrCreate(
        string|array|\ArrayAccess $values,
        ?string $type = null,
        ?string $locale = null,
    ): Collection|BaseTag|static {
        $result = parent::findOrCreate($values, $type, $locale);

        foreach ($result instanceof Collection ? $result : [$result] as $tag) {
            if ($tag instanceof self) {
                FillMissingTagTranslations::run($tag);
            }
        }

        return $result;
    }

    protected function casts(): array {
        return [
            'service' => ServiceAreas::class,
        ];
    }
}
