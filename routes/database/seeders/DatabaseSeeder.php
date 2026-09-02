<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(['email' => 'agbokpeablamvi@gmail.com'], [
            'name' => 'Super Administrateur TRENOU',
            'password' => Hash::make('TRENOU@Super2026!'),
            'status' => User::STATUS_APPROVED,
            'is_superuser' => true,
            'approved_at' => now(),
        ]);

        User::firstOrCreate(['email' => 'test@example.com'], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
        ]);

        $this->call([
            ProjetSeeder::class,
            DevisSeeder::class,
            AttestationSeeder::class,
        ]);
    }
}
