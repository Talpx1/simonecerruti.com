<?php

declare(strict_types=1);

namespace App\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class MediaPathGenerator implements PathGenerator {
    public function getPath(Media $media): string {
        return "{$this->getBasePath($media)}/";
    }

    public function getPathForConversions(Media $media): string {
        return "{$this->getBasePath($media)}/conversions/";
    }

    public function getPathForResponsiveImages(Media $media): string {
        return "{$this->getBasePath($media)}/responsive/";
    }

    private function getBasePath(Media $media): string {
        $modelSlug = str($media->model_type)->classBasename()->snake()->plural();

        return "{$modelSlug}/{$this->getModelDirectory($media)}/{$media->collection_name}";
    }

    /**
     * Files uploaded before the uuid primary keys live under the owner's old integer id — moving them would break stored urls.
     */
    private function getModelDirectory(Media $media): string {
        /** @var int|string|null $legacy_model_id */
        $legacy_model_id = $media->getAttribute('legacy_model_id');

        /** @var int|string $model_id */
        $model_id = $media->model_id;

        return (string) ($legacy_model_id ?? $model_id);
    }
}
