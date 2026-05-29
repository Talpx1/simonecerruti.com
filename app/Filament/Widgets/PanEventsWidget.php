<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class PanEventsWidget extends Widget {
    protected string $view = 'filament.widgets.pan-events';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<string, list<array{name: string, impressions: int, hovers: int, clicks: int}>>
     */
    public function getGroupedEvents(): array {
        $rows = DB::table('pan_analytics')->orderBy('name')->get();

        $groups = [];

        foreach ($rows as $row) {
            $name = (string) $row->name;
            $group = match (true) {
                str_starts_with($name, 'cta-nav-') => __('Navigation'),
                str_starts_with($name, 'cta-social-') => __('Social'),
                str_starts_with($name, 'cta-hero-') => __('Hero CTAs'),
                str_starts_with($name, 'cta-contact-') => __('Contact CTAs'),
                str_starts_with($name, 'card-') => __('Card clicks'),
                str_starts_with($name, 'section-impression-') => __('Section impressions'),
                default => __('Other'),
            };

            $groups[$group][] = [
                'name' => $name,
                'impressions' => (int) $row->impressions,
                'hovers' => (int) $row->hovers,
                'clicks' => (int) $row->clicks,
            ];
        }

        return $groups;
    }
}
