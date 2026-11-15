<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('activity_log', function (Blueprint $table): void {
            $table->json('attribute_changes')->nullable()->after('causer_id');
        });

        DB::statement(<<<'SQL'
            UPDATE activity_log
            SET attribute_changes = NULLIF(
                    jsonb_strip_nulls(jsonb_build_object('attributes', properties::jsonb->'attributes', 'old', properties::jsonb->'old')),
                    '{}'::jsonb
                )::json,
                properties = NULLIF(properties::jsonb - 'attributes' - 'old', '{}'::jsonb)::json
            WHERE jsonb_exists(properties::jsonb, 'attributes') OR jsonb_exists(properties::jsonb, 'old')
            SQL);

        Schema::table('activity_log', function (Blueprint $table): void {
            $table->dropColumn('batch_uuid');
        });
    }
};
