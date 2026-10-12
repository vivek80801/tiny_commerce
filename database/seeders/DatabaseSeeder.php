<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if(config("app.env") === "production"){
            $this->call([
                AdminSeeder::class,
                CountrySeeder::class,
                StateSeeder::class,
                DistrictSeeder::class,
            ]);
        } else {
            $this->call([
                UserSeeder::class,
                AdminSeeder::class,
                CategorySeeder::class,
                ProductSeeder::class,
                ImageSeeder::class,
                CartSeeder::class,
                CountrySeeder::class,
                StateSeeder::class,
                DistrictSeeder::class,
            ]);
        }
    }
}
