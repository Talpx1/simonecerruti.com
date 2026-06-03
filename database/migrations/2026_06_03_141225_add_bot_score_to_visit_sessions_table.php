<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('visit_sessions', function (Blueprint $table) {
            $table->unsignedTinyInteger('bot_score')->default(0)->after('device_type');
        });
    }

    public function down(): void {
        Schema::table('visit_sessions', function (Blueprint $table) {
            $table->dropColumn('bot_score');
        });
    }
};
