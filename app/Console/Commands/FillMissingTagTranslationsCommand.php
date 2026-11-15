<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\FillMissingTagTranslations;
use App\Models\Tag;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('tags:fill-missing-translations')]
#[Description('Copy the name of every tag into the locales where it is not translated yet')]
final class FillMissingTagTranslationsCommand extends Command {
    public function handle(): int {
        $updated_count = 0;

        Tag::query()->lazyById()->each(function (Tag $tag) use (&$updated_count): void {
            if (FillMissingTagTranslations::run($tag)->wasChanged()) {
                $updated_count++;
            }
        });

        $this->components->info("Updated {$updated_count} tags.");

        return self::SUCCESS;
    }
}
