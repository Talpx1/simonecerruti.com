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

        return "{$modelSlug}/{$media->model_id}/{$media->collection_name}";
    }
}
