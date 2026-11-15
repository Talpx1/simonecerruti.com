<?php

declare(strict_types=1);

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\App;
use RuntimeException;

trait RunsOnlyInLocalEnvironment {
    protected function assertLocalEnvironment(): void {
        /**
         * @var list<string>
         */
        $allowed_environments = config()->array('database.seeders_allowed_environments');

        throw_unless(App::environment($allowed_environments), RuntimeException::class, \sprintf(
            '%s seeds fake demo data and must not run outside %s — current environment is [%s].',
            static::class,
            implode('/', $allowed_environments),
            addcslashes(App::environment(), "\0..\37"),
        ));
    }
}
