<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

use function App\Helpers\generateOrderId;

class CheckoutService
{
    public function __construct(
        private Address $address,
        private Cart $cart,
        private Order $order,
        private OrderItem $orderItem,
        private Product $product,
    ) {}

    /**
     * @param  array<string, mixed>  $newAddress
     */
    public function createOrder(
        array $newAddress,
        int $userId
    ): void {
        try {
            DB::beginTransaction();
            $address = $this->createAddress($newAddress, $userId);
            $carts = $this->getCarts($userId);

            $cartsTotal = $carts->sum('line_total');
            $orderId = generateOrderId(6);

            $order = $this->createSingleOrder(
                $userId,
                (int) $address->id,
                $orderId,
                (int) $cartsTotal,
            );

            foreach ($carts as $cart) {
                $this->createOrderItem(
                    $order, $cart
                );

                $product = $this->product::find(
                    $cart->product->id
                );

                if ((int) $product->quantity >= (int) $cart->quantity) {
                    $product->quantity -= (int) $cart->quantity;
                } else {
                    $product->quantity = 0;
                }
                $product->save();

                $cart->delete();
            }
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            dd($e->getMessage());
            Log::error($e->getMessage);
        }
    }

    public function buynow(
        int $userId,
        int $productId,
    ): void {
        $attributes = [
            'user_id' => $userId,
            'product_id' => $productId,
        ];
        $cart = $this->cart::where(
            $attributes
        )->first();

        if ($cart) {
            $cart->increment('quantity');
        } else {
            $this->cart::create([
                'quantity' => 1,
                ...$attributes,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $newAddress
     */
    private function createAddress(
        array $newAddress,
        int $userId,
    ): Address {
        $address = $this->address::where(
            'user_id', $userId
        )->first();

        if ($address) {
            $address = $this->address::find(
                (int) $newAddress['address']
            );
        } else {
            $address = $this->address::create([
                'name' => $newAddress['name'],
                'user_id' => $userId,
                'country_id' => $newAddress['country'],
                'state_id' => $newAddress['state'],
                'district_id' => $newAddress['district'],
                'phone' => $newAddress['phone_number'],
                'house_number' => $newAddress['house_number'],
                'city' => $newAddress['city'],
                'address' => $newAddress['address'],
                'pin_code' => $newAddress['pin_code'],
            ]);
        }

        return $address;
    }

    private function getCarts(int $userId): Collection
    {
        $carts = $this->cart::join(
            'products', 'carts.product_id', '=', 'products.id'
        )
            ->where('user_id', $userId)
            ->select(
                'carts.*',
                'products.price',
                DB::raw('carts.quantity * products.price as line_total')
            )
            ->get();

        return $carts;
    }

    private function createSingleOrder(
        int $userId,
        int $addressId,
        string $orderId,
        int $cartsTotal,
    ): Order {
        $order = $this->order::create([
            'user_id' => $userId,
            'address_id' => $addressId,
            'order_id' => $orderId,
            'total' => $cartsTotal,
        ]);

        return $order;
    }

    private function createOrderItem(
        Order $order,
        Cart $cart,
    ): void {
        $this->orderItem::create([
            'order_id' => $order->id,
            'product_id' => $cart->product->id,
            'price' => $cart->price,
            'quantity' => $cart->quantity,
            'amount' => $cart->line_total,
        ]);
    }
}
