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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('short_description');
            $table->json('description');
            $table->json('slug');
            $table->json('external_link')->nullable();
            $table->string('client')->nullable();
            $table->json('links')->nullable();
            $table->boolean('published');
            $table->boolean('featured');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('projects');
    }
};
