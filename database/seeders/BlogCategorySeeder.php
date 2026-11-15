<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\FillMissingTagTranslations;
use App\Enums\TagTypes;
use App\Models\Tag;
use Illuminate\Database\Seeder;

final class BlogCategorySeeder extends Seeder {
    private const array CATEGORIES = [
        ['en' => 'practical', 'it' => 'pratico'],
        ['en' => 'technical', 'it' => 'tecnico'],
    ];

    public function run(): void {
        foreach (self::CATEGORIES as $names) {
            $tag = Tag::query()
                ->where('type', TagTypes::BLOG_CATEGORY->value)
                ->get()
                ->first(fn (Tag $existing): bool => $existing->getTranslation('name', 'en') === $names['en']) ?? new Tag;

            $tag->fill([
                'type' => TagTypes::BLOG_CATEGORY->value,
                'name' => $names,
                'slug' => array_map(fn (string $name): string => str($name)->slug()->toString(), $names),
            ])->save();

            FillMissingTagTranslations::run($tag);
        }
    }
}
