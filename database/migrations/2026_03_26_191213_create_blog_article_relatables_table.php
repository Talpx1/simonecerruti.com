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
        Schema::create('blog_article_relatables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_article_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->morphs('relatable');
            $table->timestamps();

            $table->unique(['blog_article_id', 'relatable_type', 'relatable_id'], 'unique_blog_article_relatable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('blog_article_relatables');
    }
};
