<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Actions\GetServiceAreaProjects;
use App\Enums\ServiceAreas;
use Illuminate\View\View;
use Livewire\Component;

final class ServicesManagementErpCrm extends Component {
    public function render(): View {
        $view = view('pages.services.management-erp-crm')
            ->with('projects', GetServiceAreaProjects::run(ServiceAreas::MANAGEMENT_ERP_CRM, 2));

        $view->layout('layouts.public.index', [
            'description' => __('Tailor-made management software, ERP and CRM for SMEs: one system stitched onto your processes. Workflow automation, real-time data, 100% your code. No more scattered spreadsheets and double data entry.'),
        ]);

        $view->title(__('Tailor-made management software, ERP and CRM'));

        return $view;
    }
}
