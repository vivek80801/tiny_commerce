<?php

namespace App\Services;

use App\Jobs\GenerateOrderInvoce;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

use function App\Helpers\generateOrderId;
use function App\Helpers\orderIdLength;

class CheckoutService
{
    public function __construct(
        private AddressService $addressService,
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
            $address = $this
                ->addressService
                ->createAddressForCheckout(
                    $newAddress,
                    $userId
                );
            $carts = $this->getCarts($userId);

            $cartsTotal = $carts->sum('line_total');
            $orderId = generateOrderId(
                orderIdLength()
            );

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

            $orderItems = $this
                ->orderItem::where(
                    "order_id",
                    $order->id
                )
                ->with('product')
                ->get();

            GenerateOrderInvoce::dispatch(
                $orderItems,
                $order,
                $userId,
            )->afterCommit();

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

    private function getCarts(int $userId): Collection
    {
        $carts = $this->cart::join(
            'products',
            'carts.product_id',
            '=',
            'products.id',
        )
            ->where('user_id', $userId)
            ->select(
                'carts.*',
                'products.price',
                DB::raw(
                    'carts.quantity * products.price as line_total'
                )
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
