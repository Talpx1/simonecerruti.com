<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        DB::transaction(function (): void {
            $names = DB::table('projects')
                ->whereNotNull('client')
                ->where('client', '!=', '')
                ->distinct()
                ->pluck('client');

            foreach ($names as $name) {
                $client_id = DB::table('clients')->insertGetId([
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('projects')->where('client', $name)->update(['client_id' => $client_id]);
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('client');
        });
    }
};
