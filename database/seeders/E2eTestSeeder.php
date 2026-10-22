<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class E2eTestSeeder extends Seeder
{
    public function run(): void
    {
        abort_unless(app()->environment('testing') && env('CIVICGUARD_E2E') === '1', 403);

        $this->call(DatabaseSeeder::class);

        $password = 'CivicGuard-E2E-Only-2026!';

        foreach ([
            ['E2E Resident', 'resident.e2e@civicguard.test', 'resident'],
            ['E2E Official', 'official.e2e@civicguard.test', 'official'],
            ['E2E Tanod', 'tanod.e2e@civicguard.test', 'tanod'],
        ] as [$name, $email, $role]) {
            User::factory()->create([
                'name' => $name,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make($password),
                'role' => $role,
            ]);
        }
    }
}
