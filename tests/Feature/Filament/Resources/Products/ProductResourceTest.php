<?php

namespace Tests\Feature\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Override;

class ProductResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Product
     */
    private $product;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->create();

        $this->user->is_admin = true;
        $this->user->save();

        $this->actingAs($this->user);

        Category::factory(2)->create();

        $this->product = Product::factory()
            ->create();
    }

    public function test_can_render_page(): void
    {
        $response = $this->get(
            ProductResource::getUrl(
                'index'
            )
        );

        $response->assertSuccessful();
    }

    public function test_list_product(): void
    {
        Livewire::test(
            ListProducts::class
        )->assertCanSeeTableRecords([$this->product]);
    }

    public function test_can_create_cart(): void
    {
        $this->assertEquals(1, Product::count());

        $file = UploadedFile::fake()
            ->image(
                'product.jpg',
                800,
                900
            );

        Livewire::test(
            CreateProduct::class
        )->fillForm([
            'name' => $this->faker()->name(),
            'price' => 3232,
            'quantity' => 32,
            'description' => $this->faker()->sentence(),
            'category_id' => 1,
            'image.filename' => $file,
        ])
            ->call('create')
            ->assertHasNoErrors();
    }
}
