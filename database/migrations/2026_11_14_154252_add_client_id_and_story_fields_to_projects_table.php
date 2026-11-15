<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('external_link')->constrained('clients');
            $table->json('starting_point')->nullable()->after('description');
            $table->json('goal')->nullable()->after('starting_point');
            $table->json('outcome')->nullable()->after('goal');
            $table->json('kind')->nullable()->after('outcome');
            $table->json('features')->nullable()->after('kind');
            $table->json('home_summary')->nullable()->after('features');
        });
    }
};
