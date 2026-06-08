<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\Project;
use Illuminate\View\View;
use Livewire\Component;

final class ServicesWebEcommercePlatforms extends Component {
    /**
     * The most relevant published project to surface as the Area 02 case study.
     * Mirrors Services::featuredProjects() so both pages share the same ranking.
     */
    private function caseProject(): ?Project {
        return Project::query()->featuredRanked()->first();
    }

    public function render(): View {
        $view = view('pages.services.web-ecommerce-platforms')
            ->with('case_project', $this->caseProject());

        // The description is layout data (not component data) so the SeoComposer,
        // which composes the layout, can emit the <meta name="description"> tag.
        $view->layout('layouts.public.index', [
            'description' => __('Tailor-made websites, e-commerce and platforms for SMEs: a site that brings in customers, sells and grows with your business. Fast, ready for Google and connected to your tools. 100% your code.'),
        ]);

        $view->title(__('Tailor-made websites, e-commerce and platforms'));

        return $view;
    }
}
