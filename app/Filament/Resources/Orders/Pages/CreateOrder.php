<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Override;

use function App\Helpers\generateOrderId;
use function App\Helpers\orderIdLength;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    #[Override]
    protected function handleRecordCreation(array $data): Model
    {
        $this->form->validate();

        $state = $this->form->getRawState();
        $order = DB::transaction(function() use ($state){
            $userId = (int) $state["user_id"];
            $orderItems = $state["order_item"];
            $address = $state["address"];
            $total = 0;

            $orderId = generateOrderId(
                orderIdLength()
            );

            $newAddress = Address::create([
                "user_id" => $userId,
                "name" => $address["name"],
                "phone" => $address["phone"],
                "pin_code" => (int) $address["pin_code"],
                "house_number" => (int) $address["house_number"],
                "city" => $address["city"],
                "address" => $address["address"],
                "country_id" => (int) $address["country_id"],
                "state_id" => (int) $address["state_id"],
                "district_id" => (int) $address["district_id"],
            ]);

            $order = Order::create([
                "user_id" => $userId,
                "address_id" => $newAddress->id,
                "order_id" => $orderId,
                "total" => $total,
            ]);

            foreach($orderItems as $orderItem)
            {
                $total += ( (int) $orderItem["amount"] * 100);

                OrderItem::create([
                    "order_id" => $order->id,
                    "product_id" => $orderItem["product_id"],
                    "price" => (int) $orderItem["price"] * 100,
                    "quantity" => (int) $orderItem["quantity"],
                    "amount" => (int) $orderItem["amount"] * 100,
                ]);
            }

            $order->update(["total" => $total]);

            return $order;
        });

        return $order;
    }
}
