<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $categories = Category::factory(20)->create();

        foreach ($categories as $category) {
            Image::factory()
                ->for($category, 'imageable')
                ->create();
        }
    }

    public function test_categroy(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(
            route('category')
        );

        $response->assertStatus(200);
    }
}
