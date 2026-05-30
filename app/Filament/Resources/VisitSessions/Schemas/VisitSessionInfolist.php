<?php

declare(strict_types=1);

namespace App\Filament\Resources\VisitSessions\Schemas;

use App\Enums\VisitMediumType;
use App\Enums\VisitSourceType;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisitSessionInfolist {
    public static function configure(Schema $schema): Schema {
        return $schema->components([
            Section::make(__('Overview'))
                ->columns(2)
                ->schema([
                    TextEntry::make('started_at')->label(__('Started at'))->dateTime('d/m/Y H:i:s'),
                    TextEntry::make('last_activity_at')->label(__('Last activity at'))->dateTime('d/m/Y H:i:s'),
                    TextEntry::make('source')
                        ->label(__('Source'))
                        ->badge()
                        ->formatStateUsing(fn (string $state) => VisitSourceType::tryFrom($state)?->getLabel() ?? $state)
                        ->color(fn (string $state) => VisitSourceType::tryFrom($state)?->getColor() ?? 'gray'),
                    TextEntry::make('medium')
                        ->label(__('Medium'))
                        ->badge()
                        ->formatStateUsing(fn (?string $state): ?string => $state === null ? null : (VisitMediumType::tryFrom($state)?->getLabel() ?? $state))
                        ->color(fn (?string $state) => $state === null ? null : (VisitMediumType::tryFrom($state)?->getColor() ?? 'gray'))
                        ->placeholder('-'),
                    TextEntry::make('campaign.name')->label(__('Campaign'))->placeholder('-'),
                    TextEntry::make('pageview_count')->label(__('Pageviews')),
                    TextEntry::make('locale')->label(__('Locale'))->badge(),
                    TextEntry::make('country')->label(__('Country'))->badge()->placeholder('-'),
                ]),

            Section::make(__('Landing'))
                ->columns(2)
                ->schema([
                    TextEntry::make('landing_path')->label(__('Landing path'))->copyable(),
                    TextEntry::make('landing_route_name')->label(__('Route name'))->placeholder('-'),
                ]),

            Section::make(__('Referrer'))
                ->columns(2)
                ->schema([
                    TextEntry::make('referrer_url')->label(__('Referrer URL'))->placeholder('-')->columnSpanFull(),
                    TextEntry::make('referrer_host')->label(__('Referrer host'))->placeholder('-'),
                ]),

            Section::make('UTM')
                ->columns(2)
                ->schema([
                    TextEntry::make('utm_source')->label('utm_source')->placeholder('-'),
                    TextEntry::make('utm_medium')->label('utm_medium')->placeholder('-'),
                    TextEntry::make('utm_campaign')->label('utm_campaign')->placeholder('-'),
                    TextEntry::make('utm_term')->label('utm_term')->placeholder('-'),
                    TextEntry::make('utm_content')->label('utm_content')->placeholder('-'),
                ]),

            Section::make(__('Visitor'))
                ->columns(2)
                ->schema([
                    IconEntry::make('consent_analytics')->label(__('Analytics consent'))->boolean(),
                    TextEntry::make('visitor_id')->label(__('Visitor ID'))->placeholder('-')->copyable(),
                    TextEntry::make('ip')->label(__('IP address'))->placeholder('-'),
                    TextEntry::make('device_type')
                        ->label(__('Device type'))
                        ->badge()
                        ->placeholder('-'),
                    TextEntry::make('user_agent')->label(__('User agent'))->placeholder('-')->columnSpanFull(),
                ]),
        ]);
    }
}
