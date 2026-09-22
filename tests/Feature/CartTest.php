<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var User
     */
    private $user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        Category::factory()->create();
        $this->product = Product::factory(2)->create();

        $this->user = User::factory()->create();
    }

    public function test_cart_should_render(): void
    {
        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
    }

    public function test_cart_should_render_for_guest(): void
    {
        $response = $this->withCookie(
            'guest_token',
            'my_token'
        )
            ->get(
                route('cart.index')
            );

        $response->assertStatus(200);
    }

    public function test_guest_cart_should_add_product(): void
    {
        $product = $this->product[0];
        $response = $this->get(route('cart.add', $product->id));

        $this->assertDatabaseHas('carts', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(302);
    }

    public function test_guest_cart_should_increment(): void
    {
        $product = $this->product[0];
        $product->quantity = 3;
        $product->save();

        $response1 = $this->get(
            route(
                'cart.add', $product->id
            )
        );

        $response2 = $this->withCookie(
            'guest_token',
            $response1
                ->getCookie('guest_token')
                ->getValue()
        )->get(
            route(
                'cart.inc', $product->id
            )
        );

        $this->assertDatabaseHas('carts', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response2->assertStatus(302);
    }

    public function test_guest_cart_should_decrement(): void
    {
        $product = $this->product[0];
        $product->quantity = 3;
        $product->save();

        $response1 = $this->get(
            route(
                'cart.add', $product->id
            )
        );

        $response2 = $this->withCookie(
            'guest_token',
            $response1
                ->getCookie('guest_token')
                ->getValue()
        )->get(
            route(
                'cart.inc', $product->id
            )
        );

        $response3 = $this->withCookie(
            'guest_token',
            $response2
                ->getCookie('guest_token')
                ->getValue()
        )->get(
            route(
                'cart.dec', $product->id
            )
        );

        $this->assertDatabaseHas('carts', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response3->assertStatus(302);
    }

    public function test_guest_cart_should_delete(): void
    {
        $product = $this->product[0];
        $product->quantity = 3;
        $product->save();

        $response1 = $this->get(
            route(
                'cart.add', $product->id
            )
        );

        $response2 = $this->withCookie(
            'guest_token',
            $response1
                ->getCookie('guest_token')
                ->getValue()
        )->get(
            route(
                'cart.inc', $product->id
            )
        );

        $response3 = $this->withCookie(
            'guest_token',
            $response2
                ->getCookie('guest_token')
                ->getValue()
        )->get(
            route(
                'cart.dec', $product->id
            )
        );

        $response4 = $this->withCookie(
            'guest_token',
            $response3
                ->getCookie('guest_token')
                ->getValue()
        )->get(
            route(
                'cart.dec', $product->id
            )
        );

        $this->assertDatabaseMissing('carts', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response4->assertStatus(302);
    }

    public function test_cart_should_render_for_user(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
    }

    public function test_user_cart_should_add_product(): void
    {
        $this->actingAs($this->user);

        $product = $this->product[0];
        $response = $this->get(route('cart.add', $product->id));

        $this->assertDatabaseHas('carts', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(302);
    }

    public function test_user_cart_should_increment(): void
    {
        $this->actingAs($this->user);

        $product = $this->product[0];
        $product->quantity = 3;
        $product->save();

        $this->get(
            route(
                'cart.add', $product->id
            )
        );

        $response = $this->get(
            route(
                'cart.inc', $product->id
            )
        );

        $this->assertDatabaseHas('carts', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(302);
    }

    public function test_user_cart_should_decrement(): void
    {
        $this->actingAs($this->user);

        $product = $this->product[0];
        $product->quantity = 3;
        $product->save();

        $this->get(
            route(
                'cart.add', $product->id
            )
        );

        $this->get(
            route(
                'cart.inc', $product->id
            )
        );

        $response = $this->get(
            route(
                'cart.dec', $product->id
            )
        );

        $this->assertDatabaseHas('carts', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(302);
    }

    public function test_user_cart_should_delete(): void
    {
        $this->actingAs($this->user);

        $product = $this->product[0];
        $product->quantity = 3;
        $product->save();

        $this->get(
            route(
                'cart.add', $product->id
            )
        );

        $this->get(
            route(
                'cart.inc', $product->id
            )
        );

        $this->get(
            route(
                'cart.dec', $product->id
            )
        );

        $response = $this->get(
            route(
                'cart.dec', $product->id
            )
        );

        $this->assertDatabaseMissing('carts', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(302);
    }

    public function test_not_guest_and_not_user_cart_should_increment(): void
    {
        $product = $this->product[0];
        $product->quantity = 3;
        $product->save();

        $this->get(
            route(
                'cart.add', $product->id
            )
        );

        $response = $this->get(
            route(
                'cart.inc', $product->id
            )
        );

        $response->assertStatus(302);
    }

    public function test_user_cart_increment_should_give_execption(): void
    {
        $this->actingAs($this->user);

        $product = $this->product[0];
        $product->quantity = 1;
        $product->save();

        $this->get(
            route(
                'cart.add', $product->id
            )
        );

        $this->get(
            route(
                'cart.inc', $product->id
            )
        );

        $response = $this->get(
            route(
                'cart.inc', $product->id
            )
        );

        $response->assertStatus(302);
    }

    public function test_not_guest_and_not_user_cart_should_decrement(): void
    {
        $product = $this->product[0];
        $product->quantity = 3;
        $product->save();

        $this->get(
            route(
                'cart.add', $product->id
            )
        );

        $response = $this->get(
            route(
                'cart.dec', $product->id
            )
        );

        $response->assertStatus(302);
    }
}
