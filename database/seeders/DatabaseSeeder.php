<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
        if (app()->isLocal()) {
            User::factory()->create([
                'name' => 'Simone',
                'email' => 'hello@simonecerruti.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]);
        }
    }
}
