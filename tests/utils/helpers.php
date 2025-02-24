<?php

declare(strict_types=1);
use Illuminate\Database\Eloquent\Model;

/**
 * Scan the migration folder and run all files found.
 */
function runMigrations(): void {
    foreach (File::allFiles(__DIR__.'/../../database/migrations') as $migration) {
        (include $migration->getRealPath())->up();
    }
}

/**
 * Drop all tables in the database.
 */
function dropAllTables(): void {
    foreach (Schema::getTableListing() as $table) {
        Schema::dropIfExists($table);
    }
}

/**
 * Drop all tables and then run all the migrations.
 *
 * @see dropAllTables(), runMigrations()
 */
function recreateAllTables(): void {
    dropAllTables();
    runMigrations();
}

/**
 * Get the table name for a model.
 *
 * @param  class-string<Model>  $model
 */
function getTableNameForModel(string $model): string {
    return (new $model)->getTable();
}

/**
 * If the provided string is a model class-string, return the table name for it,
 * otherwise return the string as is.
 */
function maybeGetTableNameForModel(string $maybe_model): string {
    return is_a($maybe_model, Model::class, true) ? getTableNameForModel($maybe_model) : $maybe_model;
}

/**
 * Get the column definition for a column in a table.
 *
 * If the provided table is a model class-string, the table name will be inferred from the model.
 *
 * @return array{name:string,type_name: string, type: string, collation: string|null, nullable: bool, default: mixed, auto_increment: bool, comment: string|null, generation: string|null}
 */
function getColumn(string $table, string $column): array {
    $table = maybeGetTableNameForModel($table);

    return array_values(Arr::where(
        Schema::getColumns($table),
        fn ($col): bool => $col['name'] === $column
    ))[0];
}
