<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

use function App\Helpers\authUser;
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
        array $newAddress
    ): void {
        DB::beginTransaction();
        try {
            $address = $this->createAddress($newAddress, authUser()->id);
            $carts = $this->getCarts(authUser()->id);

            $cartsTotal = $carts->sum('line_total');
            $orderId = generateOrderId(6);

            $order = $this->createSingleOrder(
                authUser()->id,
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

                $product->quantity -= (int) $cart->quantity;
                $product->save();

                $cart->delete();
            }
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e);
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
    public function createAddress(
        array $newAddress,
        int $userId,
    ): Address {
        $address = $this->address::where('
            user_id', authUser()->id
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

    public function getCarts(int $userId): Cart
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

    public function createSingleOrder(
        int $userId,
        int $addressId,
        string $orderId,
        int $cartsTotal,
    ) {
        $order = $this->order::create([
            'user_id' => $userId,
            'address_id' => $addressId,
            'order_id' => $orderId,
            'total' => $cartsTotal,
        ]);

        return $order;
    }

    public function createOrderItem(
        Order $order,
        Cart $cart,
    ) {
        $this->orderItem::create([
            'order_id' => $order->id,
            'product_id' => $cart->product->id,
            'price' => $cart->price,
            'quantity' => $cart->quantity,
            'amount' => $cart->line_total,
        ]);
    }
}
