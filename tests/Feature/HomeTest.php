<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<Product>
     */
    private $products;

    /**
     * @var array<Category>
     */
    private $categories;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->categories = Category::factory(5)->create();
        $this->products = Product::factory(5)->create();

        foreach ($this->categories as $category) {
            Image::factory()
                ->for($category, 'imageable')
                ->create();
        }

        foreach ($this->products as $product) {
            Image::factory()
                ->for($product, 'imageable')
                ->create();
        }
    }

    public function test_home_should_render(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    public function test_home_search_product(): void
    {
        $product = $this->products[0];

        $response = $this->get(
            route('home', [
                'search' => $product->name,
            ])
        );

        $response->assertStatus(200);
    }

    public function test_home_search_category(): void
    {
        $category = $this->categories[0];

        $response = $this->get(
            route('home', [
                'search' => $category->name,
            ])
        );

        $response->assertStatus(200);
    }

    public function test_home_search_min_max(): void
    {
        $response = $this->get(
            route('home', [
                'min_price' => 100,
                'max_price' => 500,
            ])
        );

        $response->assertStatus(200);
    }

    public function test_home_search_category_by_id(): void
    {
        $category = $this->categories[0];

        $response = $this->get(
            route('home', [
                'category' => $category->id,
            ])
        );

        $response->assertStatus(200);
    }
}
