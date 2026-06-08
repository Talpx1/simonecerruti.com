<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\Project;
use Illuminate\View\View;
use Livewire\Component;

final class ServicesConsultingAndSeo extends Component {
    private function caseProject(): ?Project {
        return Project::query()->featuredRanked()->first();
    }

    public function render(): View {
        $view = view('pages.services.consulting-and-seo')
            ->with('case_project', $this->caseProject());
        $view->layout('layouts.public.index', [
            'description' => __('Digital and SEO consulting for SMEs: understand what you really need before building. Strategy, independent technology choices and solid foundations to get you found on Google. Support for your team, when needed.'),
        ]);

        $view->title(__('Consulting and SEO: strategy first'));

        return $view;
    }
}
