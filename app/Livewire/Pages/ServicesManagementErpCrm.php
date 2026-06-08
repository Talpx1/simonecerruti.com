<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\Project;
use Illuminate\View\View;
use Livewire\Component;

final class ServicesManagementErpCrm extends Component {
    /**
     * The most relevant published project to surface as the Area 01 case study.
     * Mirrors Services::featuredProjects() so both pages share the same ranking.
     */
    private function caseProject(): ?Project {
        return Project::query()->featuredRanked()->first();
    }

    public function render(): View {
        $view = view('pages.services.management-erp-crm')
            ->with('case_project', $this->caseProject());

        // The description is layout data (not component data) so the SeoComposer,
        // which composes the layout, can emit the <meta name="description"> tag.
        $view->layout('layouts.public.index', [
            'description' => __('Tailor-made management software, ERP and CRM for SMEs: one system stitched onto your processes. Workflow automation, real-time data, 100% your code. No more scattered spreadsheets and double data entry.'),
        ]);

        $view->title(__('Tailor-made management software, ERP and CRM'));

        return $view;
    }
}
