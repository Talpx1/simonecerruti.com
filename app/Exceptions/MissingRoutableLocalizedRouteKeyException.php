<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Database\Eloquent\Model;

class MissingRoutableLocalizedRouteKeyException extends LocalizationException {
    public function __construct(string $key, string $locale, Model|string|null $model = null) {
        $msg = "Missing localized route key '{$key}' for locale '{$locale}'.";

        if (! $model) {
            parent::__construct($msg);

            return;
        }

        if ($model instanceof Model) {
            $class = $model::class;
            $id = $model->getKey();

            parent::__construct($msg." Model: {$class}#{$id}");

            return;
        }

        parent::__construct($msg." Model: {$model}");

    }
}
