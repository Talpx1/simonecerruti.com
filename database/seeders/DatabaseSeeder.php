<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Concerns\RunsOnlyInLocalEnvironment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder {
    use RunsOnlyInLocalEnvironment;

    /**
     * Seed the application's database.
     */
    public function run(): void {
        $this->assertLocalEnvironment();

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
