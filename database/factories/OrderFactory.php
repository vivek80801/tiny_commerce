<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Country;
use App\Models\District;
use App\Models\Order;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

use function App\Helpers\generateOrderId;
use function App\Helpers\orderIdLength;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderId = generateOrderId(
            orderIdLength()
        );
        if(
            User::count() > 0 &&
            Address::count() > 0
        )
        {
            $user = User::first();
            $address = Address::first();

            return [
                "user_id" => $user->id,
                "address_id" => $address->id,
                "order_id" => $orderId,
                "total" => 0,
            ];
        }else {
            if(
                User::count() <= 0
            )
            {
                User::factory()->create();
            }
            $user = User::first();
            if(
                Address::count() <= 0
            )
            {
                $country = Country::first();
                $state = State::first();
                $district = District::first();

                Address::create([
                    "user_id" => $user->id,
                    "name" => fake()->name(),
                    "phone" => 1023456789,
                    "pin_code" => 800009,
                    "house_number" => 323,
                    "city" => "patna",
                    "address" => "New address",
                    "country_id" => $country->id,
                    "state_id" => $state->id,
                    "district_id" => $district->id,
                ]);
            }

            $address = Address::first();

            return [
                "user_id" => $user->id,
                "address_id" => $address->id,
                "order_id" => $orderId,
                "total" => 0,
            ];
        }
    }
}
