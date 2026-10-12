<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CartSeeder;
use Database\Seeders\ImageSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\StateSeeder;
use Database\Seeders\DistrictSeeder;
use Database\Seeders\OrderSeeder;
use Database\Seeders\OrderItemSeeder;

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
                OrderSeeder::class,
                OrderItemSeeder::class,
            ]);
        }
    }
}
