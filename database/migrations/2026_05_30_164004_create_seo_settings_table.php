<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();

            $table->string('type');
            $table->string('name')->nullable();
            $table->json('description')->nullable();
            $table->json('social_profiles')->nullable();
            $table->string('default_og_image')->nullable();
            $table->string('title_separator');
            $table->boolean('website_schema');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('seo_settings');
    }
};
