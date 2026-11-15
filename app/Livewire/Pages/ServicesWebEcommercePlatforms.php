<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Actions\GetServiceAreaProjects;
use App\Enums\ServiceAreas;
use Illuminate\View\View;
use Livewire\Component;

final class ServicesWebEcommercePlatforms extends Component {
    public function render(): View {
        $view = view('pages.services.web-ecommerce-platforms')
            ->with('projects', GetServiceAreaProjects::run(ServiceAreas::WEB_ECOMMERCE_PLATFORMS, 2));

        $view->layout('layouts.public.index', [
            'description' => __('Tailor-made websites, e-commerce and platforms for SMEs: a site that brings in customers, sells and grows with your business. Fast, ready for Google and connected to your tools. 100% your code.'),
        ]);

        $view->title(__('Tailor-made websites, e-commerce and platforms'));

        return $view;
    }
}
