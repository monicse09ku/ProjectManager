<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed users (admin + regular users)
        $this->call(UserSeeder::class);

        // Other seeders
        $this->call(ClientSeeder::class);
    }
}
