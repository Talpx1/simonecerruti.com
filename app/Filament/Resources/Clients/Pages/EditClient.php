<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clients\Pages;

use App\Actions\DeleteClient;
use App\Filament\Resources\Clients\ClientResource;
use App\Models\Client;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditClient extends EditRecord {
    use Translatable;

    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array {
        return [
            DeleteAction::make()
                ->using(fn (Client $record): bool => DeleteClient::run($record)),
            LocaleSwitcher::make(),
        ];
    }
}
