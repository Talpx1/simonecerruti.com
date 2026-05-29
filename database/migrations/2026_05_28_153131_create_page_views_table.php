<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('page_views', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignUuid('visit_session_id')->constrained('visit_sessions')->cascadeOnDelete();
            $table->string('url_path');
            $table->string('route_name')->nullable()->index();
            $table->char('locale', 5);
            $table->timestamp('viewed_at')->index();
            $table->timestamps();

            $table->index(['visit_session_id', 'viewed_at']);
            $table->index(['viewed_at', 'route_name']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('page_views');
    }
};
