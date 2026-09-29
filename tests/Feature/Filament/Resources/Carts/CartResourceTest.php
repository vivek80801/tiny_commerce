<?php

namespace Tests\Feature\Filament\Resources\Carts;

use App\Filament\Resources\Carts\CartResource;
use App\Filament\Resources\Carts\Pages\CreateCart;
use App\Filament\Resources\Carts\Pages\EditCart;
use App\Filament\Resources\Carts\Pages\ListCarts;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Override;
use Tests\TestCase;

class CartResourceTest extends TestCase
{
    use RefreshDatabase;

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
            CartResource::getUrl(
                'index'
            )
        );

        $response->assertSuccessful();
    }

    public function test_list_cart(): void
    {
        Category::factory(2)->create();
        Product::factory(6)->create();
        $cart = Cart::create([
            'user_id' => $this->user->id,
            'product_id' => 1,
            'quantity' => 1,
            'guest_token' => null,
        ]);

        Livewire::test(ListCarts::class)
            ->assertCanSeeTableRecords([$cart]);
    }

    public function test_can_create_cart(): void
    {
        $this->assertEquals(0, Cart::count());

        Livewire::test(CreateCart::class)
            ->fillForm([
                'product_id' => $this->product->id,
                'user_id' => $this->user->id,
                'quantity' => 2,
            ])
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(Cart::class, [
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'quantity' => 2,
        ]);
    }

    public function test_can_edit_cart(): void
    {
        $cart = Cart::factory()->create();

        Livewire::test(EditCart::class, [
            'record' => $cart->getRouteKey(),
        ])
            ->assertSchemaStateSet([
                'product_id' => $this->product->id,
                'user_id' => $this->user->id,
            ]);
    }
}
