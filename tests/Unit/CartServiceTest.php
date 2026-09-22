<?php

namespace Tests\Unit;

use App\Exceptions\CartQuantityCheckException;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Override;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var CartService
     */
    private $cartService;

    /**
     * @var array<Product>
     */
    private $products;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $myToken;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = app(
            CartService::class
        );

        Category::factory()->create();

        $this->products = Product::factory(2)
            ->create();

        $this->user = User::factory()
            ->create();
        $this->myToken = 'mytoken';
    }

    public function test_cart_should_be_empty_for_guest_user(): void
    {
        $carts = $this->cartService->getCart([
            'guest_token',
            $this->myToken,
        ]);

        $this->assertEmpty($carts);
    }

    public function test_create_cart_when_user_is_null_and_guest_is_null(): void
    {
        $product = $this->products[0];
        $this->cartService->addToCart(
            $product,
            null,
            null,
        );

        $this->assertDatabaseMissing('carts', [
            'quantity' => 1,
            'product_id' => $product->id,
            'user_id' => null,
            'guest_token' => null,
        ]);
    }

    public function test_create_guest_cart(): void
    {
        $product = $this->products[0];
        $this->cartService->addToCart(
            $product,
            null,
            $this->myToken,
        );

        $this->assertDatabaseHas('carts', [
            'quantity' => 1,
            'product_id' => $product->id,
            'user_id' => null,
            'guest_token' => $this->myToken,
        ]);
    }

    public function test_get_guest_cart(): void
    {
        $product = $this->products[0];
        $this->cartService->addToCart(
            $product,
            null,
            $this->myToken,
        );

        $cart = Cart::where([
            ['product_id', $product->id],
            ['guest_token', $this->myToken],
        ])
            ->first();

        $this->assertEquals(
            $cart->product_id, $product->id
        );
    }

    public function test_increment_when_add_to_cart_same_product_guest_cart(): void
    {
        $product = $this->products[0];

        $this->cartService->addToCart(
            $product,
            null,
            $this->myToken,
        );

        $this->cartService->addToCart(
            $product,
            null,
            $this->myToken,
        );

        $this->assertDatabaseHas('carts', [
            'quantity' => 2,
            'product_id' => $product->id,
            'user_id' => null,
            'guest_token' => $this->myToken,
        ]);
    }

    public function test_increment_quantity_in_guest_cart(): void
    {
        $product = $this->products[0];

        $this->cartService->addToCart(
            $product,
            null,
            $this->myToken,
        );

        $this->cartService->increment(
            $product,
            null,
            $this->myToken,
        );

        $this->cartService->increment(
            $product,
            null,
            $this->myToken,
        );

        $this->assertDatabaseHas('carts', [
            'quantity' => 3,
            'product_id' => $product->id,
            'user_id' => null,
            'guest_token' => $this->myToken,
        ]);
    }

    public function test_increment_quantity_when_product_quantity_is_less_in_guest_cart(): void
    {
        $product = $this->products[0];
        $product->quantity = 1;
        $product->save();

        $this->cartService->addToCart(
            $product,
            null,
            $this->myToken,
        );

        $this->cartService->increment(
            $product,
            null,
            $this->myToken,
        );

        $this->assertThrows(
            fn () => $this->cartService->increment(
                $product,
                null,
                $this->myToken,
            ),
            CartQuantityCheckException::class,
            '
                Product quantity in the cart can not be greater then product stock
            '
        );
    }

    public function test_decrement_quantity_in_guest_cart(): void
    {
        $product = $this->products[0];

        $this->cartService->addToCart(
            $product,
            null,
            $this->myToken,
        );

        $this->cartService->increment(
            $product,
            null,
            $this->myToken,
        );

        $this->cartService->increment(
            $product,
            null,
            $this->myToken,
        );

        $this->cartService->decrement(
            $product,
            null,
            $this->myToken,
        );

        $this->assertDatabaseHas('carts', [
            'quantity' => 2,
            'product_id' => $product->id,
            'user_id' => null,
            'guest_token' => $this->myToken,
        ]);
    }

    public function test_decrement_delete_guest_cart_when_quantity_is_one(): void
    {
        $product = $this->products[0];

        $this->cartService->addToCart(
            $product,
            null,
            $this->myToken,
        );

        $this->cartService->increment(
            $product,
            null,
            $this->myToken,
        );

        $this->cartService->decrement(
            $product,
            null,
            $this->myToken,
        );

        $this->cartService->decrement(
            $product,
            null,
            $this->myToken,
        );

        $this->assertDatabaseMissing('carts', [
            'quantity' => 2,
            'product_id' => $product->id,
            'user_id' => null,
            'guest_token' => $this->myToken,
        ]);
    }

    public function test_cart_should_be_empty_for_user(): void
    {
        $carts = $this->cartService->getCart([
            'user_id',
            $this->user->id,
        ]);

        $this->assertEmpty($carts);
    }

    public function test_user_add_to_cart(): void
    {
        $product = $this->products[0];

        $this->cartService->addToCart(
            $product,
            $this->user->id,
            null,
        );

        $cart = Cart::where([
            ['product_id', $product->id],
            ['user_id', $this->user->id],
        ])
            ->first();

        $this->assertEquals(
            $cart->product_id, $product->id
        );
    }

    public function test_increment_when_add_to_cart_same_product_user_cart(): void
    {
        $product = $this->products[0];

        $this->cartService->addToCart(
            $product,
            $this->user->id,
            null,
        );

        $this->cartService->addToCart(
            $product,
            $this->user->id,
            null,
        );

        $this->assertDatabaseHas('carts', [
            'quantity' => 2,
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'guest_token' => null,
        ]);
    }

    public function test_increment_quantity_in_user_cart(): void
    {
        $product = $this->products[0];

        $this->cartService->addToCart(
            $product,
            $this->user->id,
            null,
        );

        $this->cartService->increment(
            $product,
            $this->user->id,
            null,
        );

        $this->cartService->increment(
            $product,
            $this->user->id,
            null,
        );

        $this->assertDatabaseHas('carts', [
            'quantity' => 3,
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'guest_token' => null,
        ]);
    }

    public function test_decrement_quantity_in_user_cart(): void
    {
        $product = $this->products[0];

        $this->cartService->addToCart(
            $product,
            $this->user->id,
            null,
        );

        $this->cartService->increment(
            $product,
            $this->user->id,
            null,
        );

        $this->cartService->increment(
            $product,
            $this->user->id,
            null,
        );

        $this->cartService->decrement(
            $product,
            $this->user->id,
            null,
        );

        $this->assertDatabaseHas('carts', [
            'quantity' => 2,
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'guest_token' => null,
        ]);
    }

    public function test_decrement_delete_user_cart_when_quantity_is_one(): void
    {
        $product = $this->products[0];

        $this->cartService->addToCart(
            $product,
            $this->user->id,
            null,
        );

        $this->cartService->increment(
            $product,
            $this->user->id,
            null,
        );

        $this->cartService->decrement(
            $product,
            $this->user->id,
            null,
        );

        $this->cartService->decrement(
            $product,
            $this->user->id,
            null,
        );

        $this->assertDatabaseMissing('carts', [
            'quantity' => 2,
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'guest_token' => null,
        ]);
    }

    public function test_transfer_guest_cart_if_user_cart_exists(): void
    {
        $product = $this->products[0];

        $this->cartService->addToCart(
            $product,
            null,
            $this->myToken,
        );

        $this->cartService->addToCart(
            $product,
            $this->user->id,
            null,
        );

        $this->cartService->transferGuestCartToUserCart(
            $this->myToken,
            $this->user->id
        );

        $this->assertDatabaseHas('carts', [
            'quantity' => 2,
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'guest_token' => null,
        ]);
    }

    public function test_transfer_guest_cart_if_user_cart_does_not_exists(): void
    {
        $product = $this->products[0];

        $this->cartService->addToCart(
            $product,
            null,
            $this->myToken,
        );

        $this->cartService->transferGuestCartToUserCart(
            $this->myToken,
            $this->user->id
        );

        $this->assertDatabaseHas('carts', [
            'quantity' => 1,
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'guest_token' => null,
        ]);
    }

    public function test_transfer_guest_cart_if_user_cart_exists_but_are_completely_one_to_one(): void
    {
        $product1 = $this->products[0];
        $product2 = $this->products[1];

        $this->cartService->addToCart(
            $product1,
            null,
            $this->myToken,
        );

        $this->cartService->addToCart(
            $product1,
            null,
            $this->myToken,
        );

        $this->cartService->addToCart(
            $product2,
            null,
            $this->myToken,
        );

        $this->cartService->addToCart(
            $product1,
            $this->user->id,
            null,
        );

        $this->cartService->transferGuestCartToUserCart(
            $this->myToken,
            $this->user->id
        );

        $this->assertDatabaseHas('carts', [
            'quantity' => 2,
            'product_id' => $product1->id,
            'user_id' => $this->user->id,
            'guest_token' => null,
        ]);

        $this->assertDatabaseHas('carts', [
            'quantity' => 1,
            'product_id' => $product2->id,
            'user_id' => $this->user->id,
            'guest_token' => null,
        ]);
    }
}
