<?php

declare(strict_types=1);

namespace App\Services\MediaLibrary;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class ConfigBasedPathGenerator implements PathGenerator {
    public function getPath(Media $media): string {
        $config_slug = Str::snake(class_basename($media->model_type)).".{$media->collection_name}";
        $config = config()->array($config_slug);

        return Str::replacePlaceholders($config['path'], $media->model).'/';
    }

    public function getPathForConversions(Media $media): string {
        return $this->getPath($media).'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string {
        return $this->getPath($media).'/responsive/';
    }
}
