<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Contracts\SyncsToDatabase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionEnum;
use Symfony\Component\Console\Attribute\AsCommand;
use UnitEnum;

#[AsCommand(name: 'enums:sync')]
class SyncEnums extends Command {
    protected $description = 'Sync all enums that implement SyncsToDatabase to the database';

    public function handle(): int {
        $enum_files = File::allFiles(app_path('Enums'));

        $synced = 0;

        foreach ($enum_files as $file) {
            $class = 'App\\Enums\\'.$file->getFilenameWithoutExtension();

            if (! enum_exists($class)) {
                continue;
            }

            $reflection = new ReflectionEnum($class);

            if (! $reflection->implementsInterface(SyncsToDatabase::class)) {
                continue;
            }

            $this->line("Syncing <info>{$class}</info>...");

            try {
                /** @var class-string<UnitEnum&SyncsToDatabase> $class */
                $class::sync();
                $synced++;
                $this->line('  <info>✓</info> Done');
            } catch (\RuntimeException $e) {
                $this->line("  <fg=red>✗</> {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Synced {$synced} enum(s).");

        return self::SUCCESS;
    }
}
