<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->name(),
            "price" => fake()->numberBetween(10000, 100000),
            "description" => fake()->text(100),
            "quantity" => fake()->numberBetween(10, 50),
            "category_id" => Category::inRandomOrder()->first()->id,
        ];
    }
}
