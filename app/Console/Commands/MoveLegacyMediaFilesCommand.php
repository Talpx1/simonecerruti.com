<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\MoveLegacyMediaFiles;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'media:move-legacy-files', description: 'Move files stored under the old integer model id to the UUID path')]
final class MoveLegacyMediaFilesCommand extends Command {
    public function handle(): int {
        $moved_count = MoveLegacyMediaFiles::run();

        $this->info("Moved {$moved_count} media item(s).");

        return self::SUCCESS;
    }
}
