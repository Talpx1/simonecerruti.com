<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\LogsAllDirtyChanges;
use Carbon\CarbonImmutable;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read Collection<int, Project> $projects
 * @property-read string|null $image_url
 */
#[Fillable(['name', 'description'])]
class Client extends Model implements HasMedia {
    /** @use HasFactory<ClientFactory> */
    use HasFactory, HasTranslations, InteractsWithMedia, LogsAllDirtyChanges;

    /** @var list<string> */
    public array $translatable = ['description'];

    /** @return HasMany<Project, $this> */
    public function projects(): HasMany {
        return $this->hasMany(Project::class);
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('client_image')
            ->singleFile()
            ->useDisk(config()->string('client.image.disk'))
            ->acceptsMimeTypes(config()->array('client.image.accepted_mimes'));
    }

    public function registerMediaConversions(?Media $media = null): void {
        $final_size_px = config()->integer('client.image.final_size_px');

        $conversion = $this->addMediaConversion('client_image_webp')
            ->performOnCollections('client_image')
            ->format('webp')
            ->quality(config()->integer('client.image.quality'))
            ->fit(Fit::Crop, $final_size_px, $final_size_px);

        if (config()->boolean('client.image.optimize')) {
            $conversion->optimize();
        }
    }

    /** @return Attribute<non-empty-string|null, never> */
    protected function imageUrl(): Attribute {
        return Attribute::get(function (): ?string {
            $image_url = $this->getFirstMediaUrl('client_image', 'client_image_webp')
                ?: $this->getFirstMediaUrl('client_image');

            return $image_url === '' ? null : $image_url;
        });
    }
}
