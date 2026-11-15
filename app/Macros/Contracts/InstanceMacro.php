<?php

declare(strict_types=1);

namespace App\Macros\Contracts;

/**
 * For macros called on an instance (`$request->queryStringOrNull()`): `handle()` receives
 * that instance as its first argument — a static method has no `$this` to read it from.
 *
 * @method static mixed handle(mixed $instance, mixed ...$arguments)
 */
abstract class InstanceMacro extends Macro {
    public static function register(): void {
        $macro_class = static::class;

        static::macroable()::macro(static::name(), function (mixed ...$arguments) use ($macro_class): mixed {
            return $macro_class::handle($this, ...$arguments);
        });
    }
}
