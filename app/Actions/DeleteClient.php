<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class DeleteClient {
    use AsAction;

    public function handle(Client $client): bool {
        return DB::transaction(function () use ($client): bool {
            $client->projects()->update(['client_id' => null]);

            return (bool) $client->delete();
        });
    }
}
