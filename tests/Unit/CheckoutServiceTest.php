<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\Category;
use App\Models\District;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use App\Services\CheckoutService;
use Database\Seeders\CountrySeeder;
use Database\Seeders\DistrictSeeder;
use Database\Seeders\StateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Override;

class CheckoutServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var CheckoutService
     */
    private $checkoutService;

    /**
     * @var CartService
     */
    private $cartService;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var array<Address>
     */
    private $address;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            CountrySeeder::class,
            StateSeeder::class,
            DistrictSeeder::class,
        ]);

        Category::factory()
            ->create();
        $this->product = Product::factory(2)
            ->create();
        $this->cartService = app(
            CartService::class
        );
        $this->checkoutService = app(
            CheckoutService::class
        );
        $this->user = User::factory()
            ->create();
        $state_id = $this
            ->faker()
            ->numberBetween(
                1,
                28
            );
        $district_id = District::where(
            'state_id',
            $state_id
        )->first()->id;

        $this->address = [
            'name' => $this->faker()->name,
            'user_id' => $this->user->id,
            'country' => 1,
            'state' => $state_id,
            'district' => $district_id,
            'phone_number' => 1023456789,
            'house_number' => 12,
            'city' => 'Patna',
            'address' => 'Something Something',
            'pin_code' => 800009,
        ];
    }

    public function test_checkout_create_order_when_no_address(): void
    {
        $product = $this->product[0];

        $this->cartService->addToCart(
            $product,
            $this->user->id,
            null,
        );

        $this->checkoutService->createOrder(
            $this->address,
            $this->user->id,
        );

        $this->assertDatabaseHas(
            'addresses',
            [
                'name' => $this->address['name'],
                'user_id' => $this->user->id,
                'country_id' => $this->address['country'],
                'state_id' => $this->address['state'],
                'district_id' => $this->address['district'],
                'phone' => $this->address['phone_number'],
                'house_number' => 12,
                'city' => 'Patna',
                'address' => 'Something Something',
                'pin_code' => 800009,
            ]
        );

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
            'amount' => $product->price,
        ]);
    }

    public function test_checkout_create_order_when_address(): void
    {
        $product = $this->product[0];

        $address = Address::create([
            'name' => $this->address['name'],
            'user_id' => $this->user->id,
            'country_id' => $this->address['country'],
            'state_id' => $this->address['state'],
            'district_id' => $this->address['district'],
            'phone' => $this->address['phone_number'],
            'house_number' => 12,
            'city' => 'Patna',
            'address' => 'Something Something',
            'pin_code' => 800009,
        ]);

        $this->cartService->addToCart(
            $product,
            $this->user->id,
            null,
        );

        $newAddress = [
            'address' => $address->id,
        ];

        $this->checkoutService->createOrder(
            $newAddress,
            $this->user->id,
        );

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'address_id' => $address->id,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
            'amount' => $product->price,
        ]);
    }

    public function test_checkout_create_order_when_quantity_equal_then_product_quantity(): void
    {
        $product = $this->product[0];

        $product->quantity = 1;
        $product->save();

        $address = Address::create([
            'name' => $this->address['name'],
            'user_id' => $this->user->id,
            'country_id' => $this->address['country'],
            'state_id' => $this->address['state'],
            'district_id' => $this->address['district'],
            'phone' => $this->address['phone_number'],
            'house_number' => 12,
            'city' => 'Patna',
            'address' => 'Something Something',
            'pin_code' => 800009,
        ]);

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

        $newAddress = [
            'address' => $address->id,
        ];

        $this->checkoutService->createOrder(
            $newAddress,
            $this->user->id,
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 0,
        ]);
    }

    public function test_buy_now(): void
    {
        $product = $this->product[0];

        $this->checkoutService->buynow(
            $this->user->id,
            $product->id,
        );

        $this->assertDatabaseHas('carts', [
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'quantity' => 1,
        ]);
    }

    public function test_buy_now_if_cart_already_exists(): void
    {
        $product = $this->product[0];

        $this->cartService->addToCart(
            $product,
            $this->user->id,
            null,
        );

        $this->checkoutService->buynow(
            $this->user->id,
            $product->id,
        );

        $this->assertDatabaseHas('carts', [
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'quantity' => 2,
        ]);
    }
}
