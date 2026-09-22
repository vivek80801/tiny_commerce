<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\Country;
use App\Models\District;
use App\Models\Order;
use App\Models\Product;
use App\Models\State;
use App\Models\User;
use App\Services\CartService;
use App\Services\CheckoutService;
use Database\Seeders\CountrySeeder;
use Database\Seeders\DistrictSeeder;
use Database\Seeders\StateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Override;
use Tests\TestCase;

class CheckoutTest extends TestCase
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

    public function test_checkout_should_render(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(
            route('checkout')
        );

        $response->assertStatus(200);
    }

    public function test_checkout_should_render_with_country(): void
    {
        $this->actingAs($this->user);

        $country = Country::first();
        $response = $this
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            ])
            ->get(
                route('checkout'),
                [
                    'country_id' => $country->id,
                ]
            );

        $response->assertStatus(200);
    }

    public function test_checkout_should_render_with_state(): void
    {
        $this->actingAs($this->user);

        $state = State::first();
        $response = $this
            ->withHeaders([
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            ])
            ->get(
                route('checkout'),
                [
                    'state_id' => $state->id,
                ]
            );

        $response->assertStatus(200);
    }

    public function test_checkout_post(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(
            route('checkout'),
            [
                'name' => $this->address['name'],
                'phone_number' => $this->address['phone_number'],
                'country' => $this->address['country'],
                'state' => $this->address['state'],
                'district' => $this->address['district'],
                'pin_code' => $this->address['pin_code'],
                'address' => $this->address['address'],
                'house_number' => $this->address['house_number'],
                'city' => $this->address['city'],
            ]
        );

        $response->assertStatus(302);
    }

    public function test_buy_now(): void
    {
        $product = $this->product[0];
        $this->actingAs($this->user);

        $response = $this->get(
            route('buynow', $product->id),
        );

        $response->assertStatus(302);
    }

    public function test_order_detail(): void
    {
        $this->checkoutService->createOrder(
            $this->address,
            $this->user->id,
        );

        $order = Order::where(
            'user_id',
            $this->user->id
        )
            ->first();

        $this->actingAs($this->user);

        $response = $this->get(
            route('orderdetail', $order->id)
        );

        $response->assertStatus(200);
    }
}
