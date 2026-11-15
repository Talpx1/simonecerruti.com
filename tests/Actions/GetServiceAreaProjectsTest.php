<?php

declare(strict_types=1);

use App\Actions\GetServiceAreaProjects;
use App\Enums\ServiceAreas;
use App\Models\Project;

describe('service area projects', function (): void {
    it('returns only published projects tagged with the given service area', function (): void {
        $tag = createServiceTag(ServiceAreas::WEB_ECOMMERCE_PLATFORMS, 'Website');
        $other_tag = createServiceTag(ServiceAreas::CONSULTING_AND_SEO, 'SEO');

        $matching = Project::factory()->published()->create();
        $matching->attachTag($tag);
        $unpublished = Project::factory()->unpublished()->create();
        $unpublished->attachTag($tag);
        $other_area = Project::factory()->published()->create();
        $other_area->attachTag($other_tag);
        Project::factory()->published()->create();

        $result = GetServiceAreaProjects::run(ServiceAreas::WEB_ECOMMERCE_PLATFORMS, 5);

        expect($result->pluck('id')->all())->toBe([$matching->id]);
    });

    it('ranks featured projects first and honours the limit', function (): void {
        $tag = createServiceTag(ServiceAreas::MANAGEMENT_ERP_CRM, 'CRM');
        $regular = Project::factory()->published()->create();
        $featured = Project::factory()->published()->featured()->create();
        $regular->attachTag($tag);
        $featured->attachTag($tag);

        $result = GetServiceAreaProjects::run(ServiceAreas::MANAGEMENT_ERP_CRM, 1);

        expect($result->pluck('id')->all())->toBe([$featured->id]);
    });

    it('eager loads the relations the views read', function (): void {
        $tag = createServiceTag(ServiceAreas::MANAGEMENT_ERP_CRM, 'CRM');
        Project::factory()->published()->create()->attachTag($tag);

        $project = GetServiceAreaProjects::run(ServiceAreas::MANAGEMENT_ERP_CRM, 1)->first();

        expect($project->relationLoaded('tags'))->toBeTrue()
            ->and($project->relationLoaded('client'))->toBeTrue()
            ->and($project->relationLoaded('media'))->toBeTrue();
    });
});
