<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

use function App\Helpers\authUser;
use function App\Helpers\createGuestToken;
use function App\Helpers\getGuestToken;

class CartService
{
    public function __construct
    (
        private Cart $cart,
    ) { }

    public function getCart(): LengthAwarePaginator
    {
        $query = $this->cart::query();

        if (Auth::check()) {
            $query = $query->where(
                'user_id', authUser()->id
            );
        } else {
            $token = getGuestToken();

            if ($token) {
                $query = $query->where('guest_token', $token);
            }
        }

        $carts = $query->paginate(10);

        return $carts;
    }

    public function addToCart(Product $product): void
    {
        $token = getGuestToken();
        $userId = Auth::check() ? authUser()->id : null;

        if(!Auth::check() && !$token)
        {
            $uuid = createGuestToken();
            $this->createGuestCart(
                $product,
                $uuid,
            );

            $token = $uuid;
        }

        $cart = $this->getTheCart(
            $product,
            $token,
            $userId,
        );

        if($cart)
        {
            $cart->increment("quantity");
        } else if(Auth::check()) {

            $this->createUserCart($product, $userId);
        } else if($token) {

            $this->createGuestCart($product, $token);
        }
    }

    public function increment(Product $product): void
    {
        $token = getGuestToken();
        $userId = Auth::check() ? authUser()->id : null;

        $cart = $this->getTheCart(
            $product,
            $token,
            $userId,
        );

        if($cart)
        {
            $cart->increment('quantity');
        }
    }

    public function decrement(Product $product): Cart
    {
        $token = getGuestToken();
        $userId = Auth::check() ? authUser()->id : null;

        $cart = $this->getTheCart($product, $token, $userId);
        if($cart)
        {
            if ($cart->quantity <= 1) {
                $cart->delete();

                return $cart;
            }
            $cart->decrement('quantity');
        }

        return $cart;
    }

    public function createGuestCart
    (
        Product $product,
        string $token,
    ): Cart
    {
        $cart = $this->cart::create([
            'quantity' => 1,
            'product_id' => $product->id,
            'guest_token' => $token,
        ]);

        return $cart;
    }

    public function createUserCart
    (
        Product $product,
        int $userId
    ): Cart
    {
        $cart = $this->cart::create([
            'quantity' => 1,
            'product_id' => $product->id,
            'user_id' => $userId,
        ]);

        return $cart;
    }

    public function getTheCart
    (
        Product $product,
        ?string $token,
        ?int $userId,
    ): Cart
    {
        $attributes = [
            ['product_id', $product->id],
        ];

        if (Auth::check()) {
            array_push(
                $attributes,
                ["user_id" => $userId],
            );
        } else {
            if ($token) {
                array_push(
                    $attributes,
                    ["guest_token" => $token],
                );
            }
        }

        $cart = $this
            ->cart::where($attributes)
            ->first();

        return $cart;
    }
}
