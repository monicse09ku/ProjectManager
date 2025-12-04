<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create first client with one project
        $client1 = Client::create([
            'client_name' => $faker->company(),
        ]);

        $projectTitle1 = $faker->sentence(3);
        Project::create([
            'client_id' => $client1->id,
            'title' => $projectTitle1,
            'slug' => Str::slug($projectTitle1),
            'start_date' => $faker->dateTimeBetween('-6 months')->format('Y-m-d'),
            'end_date' => $faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'status' => $faker->randomElement(['active', 'inactive', 'archived']),
        ]);

        // Create second client with one project
        $client2 = Client::create([
            'client_name' => $faker->company(),
        ]);

        $projectTitle2 = $faker->sentence(3);
        Project::create([
            'client_id' => $client2->id,
            'title' => $projectTitle2,
            'slug' => Str::slug($projectTitle2),
            'start_date' => $faker->dateTimeBetween('-6 months')->format('Y-m-d'),
            'end_date' => $faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'status' => $faker->randomElement(['active', 'inactive', 'archived']),
        ]);
    }
}
