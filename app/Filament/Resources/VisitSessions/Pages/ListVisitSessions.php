<?php

declare(strict_types=1);

namespace App\Filament\Resources\VisitSessions\Pages;

use App\Filament\Resources\VisitSessions\VisitSessionResource;
use Filament\Resources\Pages\ListRecords;

class ListVisitSessions extends ListRecords {
    protected static string $resource = VisitSessionResource::class;
}
