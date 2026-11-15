<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Actions\GetServiceAreaProjects;
use App\Enums\ServiceAreas;
use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class Services extends Component {
    /**
     * @return Collection<string, Project|null>
     */
    private function getExampleProjectPerArea(): Collection {
        return collect(ServiceAreas::cases())->mapWithKeys(
            fn (ServiceAreas $area): array => [$area->value => GetServiceAreaProjects::run($area, 1)->first()]
        );
    }

    public function render(): View {
        $view = view('pages.services.services')
            ->with('area_projects', $this->getExampleProjectPerArea());

        $view->layout('layouts.public.index', [
            'description' => __('Management software, ERP, CRM, websites, e-commerce, web apps and consulting, all tailor-made.'),
        ]);

        $view->title(__('Services'));

        return $view;
    }
}
