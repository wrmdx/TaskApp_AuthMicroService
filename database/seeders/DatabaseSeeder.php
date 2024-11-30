<?php

namespace Database\Seeders;

use App\Model\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create specific users first
        User::factory()->create([
            'name' => 'Meriem',
            'email' => 'meriem@example.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => null,
        ]);

        User::factory()->create([
            'name' => 'Mohammed',
            'email' => 'mohammed@example.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => null,
        ]);

        User::factory(8)->create();
    }
}
