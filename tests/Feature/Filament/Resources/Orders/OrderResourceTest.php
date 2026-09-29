<?php

namespace Tests\Feature\Filament\Resources\Orders;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\OrderItem;
use App\Models\Address;
use App\Models\Category;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderItem as ModelsOrderItem;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CountrySeeder;
use Database\Seeders\DistrictSeeder;
use Database\Seeders\StateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Override;
use Tests\TestCase;

use function App\Helpers\generateOrderId;

class OrderResourceTest extends TestCase
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

    /**
     * @var Address
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

        $this->user = User::factory()
            ->create();

        $this->user->is_admin = true;
        $this->user->save();

        $this->actingAs($this->user);
        Category::factory(2)->create();

        $this->product = Product::factory()
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

        $this->address = Address::create([
            'name' => $this->faker()->name,
            'user_id' => $this->user->id,
            'country_id' => 1,
            'state_id' => $state_id,
            'district_id' => $district_id,
            'phone' => 1023456789,
            'house_number' => 12,
            'city' => 'Patna',
            'address' => 'Something Something',
            'pin_code' => 800009,
        ]);
    }

    public function test_can_render_page(): void
    {
        $response = $this->get(
            OrderResource::getUrl(
                'index'
            )
        );

        $response->assertSuccessful();
    }

    public function test_list_order(): void
    {
        Order::create([
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'order_id' => generateOrderId(6),
            'total' => 3333,
        ]);

        $order = Order::where(
            'user_id',
            $this->user->id
        )->get();

        Livewire::test(
            ListOrders::class
        )->assertCanSeeTableRecords($order);
    }

    public function test_can_create_order(): void
    {
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

        $this->assertEquals(0, Order::count());

        Livewire::test(CreateOrder::class)
            ->fillForm([
                'user_id' => $this->user->id,
                'address.name' => 'something',
                'address.phone' => 1230456789,
                'address.city' => $this->faker()->name(),
                'address.pin_code' => 800003,
                'address.house_number' => 23,
                'address.address' => 'something',
                'address.country_id' => 1,
                'address.state_id' => $state_id,
                'address.district_id' => $district_id,
                'order_item' => [
                    [
                        'product_id' => $this->product->id,
                        'price' => $this->product->price,
                        'quantity' => 2,
                        'amount' => $this->product->price * 2,
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoErrors();
    }

    public function test_can_edit_order(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'order_id' => generateOrderId(6),
            'total' => 3333,
        ]);

        Livewire::test(EditOrder::class, [
            'record' => $order->getRouteKey(),
        ])
            ->assertSchemaStateSet([
                'user_id' => $this->user->id,
                'address_id' => $this->address->id,
            ]);

    }

    public function test_order_item_should_render(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'order_id' => generateOrderId(6),
            'total' => 2 * $this->product->price,
        ]);

        $orderItem = ModelsOrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'price' => $this->product->price,
            'quantity' => 2,
            'amount' => 2 * $this->product->price,
        ]);

        Livewire::test(
            OrderItem::class,
            [
                'record' => $order->getRouteKey(),
            ]
        )
            ->assertSee($order->id)
            ->assertSee($this->product->name)
            ->assertSee((string) $orderItem->quantity);
    }
}
