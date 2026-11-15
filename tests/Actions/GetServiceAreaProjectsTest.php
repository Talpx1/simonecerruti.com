<?php

declare(strict_types=1);

use App\Actions\GetServiceAreaProjects;
use App\Enums\ServiceAreas;
use App\Enums\TagTypes;
use App\Models\Project;
use App\Models\Tag;

function createServiceTag(ServiceAreas $area, string $name): Tag {
    $tag = Tag::findOrCreate($name, TagTypes::TAG->value);
    $tag->update(['service' => $area]);

    return $tag;
}

it('returns only published projects tagged with the given service area', function () {
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

it('ranks featured projects first and honours the limit', function () {
    $tag = createServiceTag(ServiceAreas::MANAGEMENT_ERP_CRM, 'CRM');
    $regular = Project::factory()->published()->create();
    $featured = Project::factory()->published()->featured()->create();
    $regular->attachTag($tag);
    $featured->attachTag($tag);

    $result = GetServiceAreaProjects::run(ServiceAreas::MANAGEMENT_ERP_CRM, 1);

    expect($result->pluck('id')->all())->toBe([$featured->id]);
});

it('eager loads the relations the views read', function () {
    $tag = createServiceTag(ServiceAreas::MANAGEMENT_ERP_CRM, 'CRM');
    Project::factory()->published()->create()->attachTag($tag);

    $project = GetServiceAreaProjects::run(ServiceAreas::MANAGEMENT_ERP_CRM, 1)->first();

    expect($project->relationLoaded('tags'))->toBeTrue()
        ->and($project->relationLoaded('client'))->toBeTrue()
        ->and($project->relationLoaded('media'))->toBeTrue();
});
