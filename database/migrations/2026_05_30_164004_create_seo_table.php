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
        Schema::create('seo', function (Blueprint $table) {
            $table->id();

            $table->morphs('seoable');
            $table->unique(['seoable_type', 'seoable_id']);

            // Translatable. If null fall back to the model default.
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->json('og_title')->nullable();
            $table->json('og_description')->nullable();
            $table->json('og_image')->nullable();
            $table->json('twitter_title')->nullable();
            $table->json('twitter_description')->nullable();
            $table->json('twitter_image')->nullable();
            $table->json('canonical')->nullable();
            $table->json('robots')->nullable();
            $table->json('schema_overrides')->nullable();

            // Non-translatable.
            $table->string('schema_type')->nullable();
            $table->string('twitter_card')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('seo');
    }
};
