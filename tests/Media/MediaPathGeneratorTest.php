<?php

declare(strict_types=1);

use App\Media\MediaPathGenerator;
use App\Models\BlogArticle;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

describe('paths', function (): void {
    it('builds paths from the model class, id and collection name', function (): void {
        $media = new Media;
        $media->model_type = BlogArticle::class;
        $media->model_id = '019a2b3c-4d5e-7f60-8a1b-2c3d4e5f6a7b';
        $media->collection_name = 'featured_image';

        $generator = new MediaPathGenerator;

        expect($generator->getPath($media))->toBe('blog_articles/019a2b3c-4d5e-7f60-8a1b-2c3d4e5f6a7b/featured_image/')
            ->and($generator->getPathForConversions($media))->toBe('blog_articles/019a2b3c-4d5e-7f60-8a1b-2c3d4e5f6a7b/featured_image/conversions/')
            ->and($generator->getPathForResponsiveImages($media))->toBe('blog_articles/019a2b3c-4d5e-7f60-8a1b-2c3d4e5f6a7b/featured_image/responsive/');
    });

    it('keeps the legacy integer model id in the paths of media uploaded before the uuid conversion', function (): void {
        $media = new Media;
        $media->model_type = BlogArticle::class;
        $media->model_id = '019a2b3c-4d5e-7f60-8a1b-2c3d4e5f6a7b';
        $media->collection_name = 'featured_image';
        $generator = new MediaPathGenerator;

        expect($generator->getPath($media))->toBe('blog_articles/019a2b3c-4d5e-7f60-8a1b-2c3d4e5f6a7b/featured_image/');

        $media->setAttribute('legacy_model_id', 5);

        expect($generator->getPath($media))->toBe('blog_articles/5/featured_image/')
            ->and($generator->getPathForConversions($media))->toBe('blog_articles/5/featured_image/conversions/')
            ->and($generator->getPathForResponsiveImages($media))->toBe('blog_articles/5/featured_image/responsive/');
    });

    it('keeps the media of different models apart', function (): void {
        $first_media = new Media;
        $first_media->model_type = BlogArticle::class;
        $first_media->model_id = '019a2b3c-4d5e-7f60-8a1b-2c3d4e5f6a7b';
        $first_media->collection_name = 'featured_image';
        $second_media = new Media;
        $second_media->model_type = BlogArticle::class;
        $second_media->model_id = '019a2b3c-4d5e-7f60-8a1b-2c3d4e5f6a7c';
        $second_media->collection_name = 'featured_image';

        $generator = new MediaPathGenerator;

        expect($generator->getPath($first_media))->not->toBe($generator->getPath($second_media));
    });
});
