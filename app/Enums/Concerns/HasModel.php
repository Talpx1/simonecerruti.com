<?php

declare(strict_types=1);

namespace App\Enums\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @template TModel of Model
 */
trait HasModel {
    /**
     * @return class-string<TModel>
     */
    private static function guessModelClass(): string {
        /** @var class-string<TModel> $model */
        $model = 'App\\Models\\'.Str::singular(class_basename(__CLASS__));

        return $model;
    }

    /** @return class-string<TModel> */
    private static function getOrGuessModelClass(): string {
        if (method_exists(static::class, 'getModelClass')) {
            /** @var class-string<TModel> $explicit */
            $explicit = static::getModelClass();

            return $explicit;
        }

        $model = static::guessModelClass();

        if (! class_exists($model)) {
            throw new \Exception('Model for enum '.__CLASS__." could not be guessed ({$model} model does not exist). Manually specify the model via the getModelClass static method.");
        }

        return $model;
    }

    /**
     * @return TModel
     */
    public function model(): Model {
        $class = static::getOrGuessModelClass();

        return $class::findOrFail($this->value);
    }
}
