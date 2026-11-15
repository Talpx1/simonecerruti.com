<?php

declare(strict_types=1);

namespace App\Settings;

use Illuminate\Support\Facades\App;
use Spatie\LaravelSettings\Settings;

final class BlogSettings extends Settings {
    /** @var array<string, string> */
    public array $author_bio;

    public static function group(): string {
        return 'blog';
    }

    public function getAuthorBioForCurrentLocale(): ?string {
        $bio = $this->author_bio[App::getLocale()] ?? null;

        return filled($bio) ? $bio : null;
    }
}
