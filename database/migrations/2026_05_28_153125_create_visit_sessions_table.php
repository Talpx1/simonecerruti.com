<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('visit_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('visitor_id')->nullable()->index();
            $table->string('source');
            $table->string('medium')->nullable();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->text('referrer_url')->nullable();
            $table->string('referrer_host')->nullable()->index();
            $table->string('landing_path');
            $table->string('landing_route_name')->nullable();
            $table->char('locale', 5);
            $table->ipAddress('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type')->nullable();
            $table->char('country', 2)->nullable();
            $table->boolean('consent_analytics')->default(false);
            $table->timestamp('started_at')->index();
            $table->timestamp('last_activity_at')->index();
            $table->unsignedInteger('pageview_count')->default(0);
            $table->timestamps();

            $table->index(['started_at', 'source']);
            $table->index(['campaign_id', 'started_at']);
            $table->index(['locale', 'started_at']);
            $table->index(['country', 'started_at']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('visit_sessions');
    }
};
