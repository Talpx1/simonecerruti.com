<?php

declare(strict_types=1);

use App\Media\MediaPathGenerator;
use App\Models\BlogArticle;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('builds paths from the model class, id and collection name', function () {
    $media = new Media;
    $media->model_type = BlogArticle::class;
    $media->model_id = 5;
    $media->collection_name = 'featured_image';

    $generator = new MediaPathGenerator;

    expect($generator->getPath($media))->toBe('blog_articles/5/featured_image/')
        ->and($generator->getPathForConversions($media))->toBe('blog_articles/5/featured_image/conversions/')
        ->and($generator->getPathForResponsiveImages($media))->toBe('blog_articles/5/featured_image/responsive/');
});
