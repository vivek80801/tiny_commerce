<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Error;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function App\Helpers\authUser;
use function App\Helpers\createGuestToken;
use function App\Helpers\getGuestToken;

class CartService
{
    public function __construct(
        private Cart $cart,
    ) {}

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
                $query = $query->where(
                    'guest_token',
                    $token
                );
            }
        }

        $query->join(
            'products as p',
            'carts.product_id',
            '=',
            'p.id',
        )->select(
            'carts.*',
            'p.price',
            DB::raw(
                'carts.quantity
                    *
                    p.price as lineTotal'
            )
        )
            ->with('product.image:id,filename,imageable_id');

        $carts = $query
            ->orderByDesc('created_at')
            ->paginate(10);

        return $carts;
    }

    public function addToCart(Product $product): void
    {
        $token = getGuestToken();
        $userId = Auth::check() ? authUser()->id : null;

        if (! Auth::check() && ! $token) {
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

        if ($cart) {
            $this->checkProductQuantityForIncreament(
                $product,
                $cart,
            );

            $cart->increment('quantity');
        } elseif (Auth::check()) {

            $this->createUserCart($product, $userId);
        } elseif ($token) {

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

        if ($cart) {
            $this->checkProductQuantityForIncreament(
                $product,
                $cart,
            );
            $cart->increment('quantity');
        }
    }

    public function decrement(Product $product): Cart
    {
        $token = getGuestToken();
        $userId = Auth::check() ? authUser()->id : null;

        $cart = $this->getTheCart(
            $product,
            $token,
            $userId,
        );

        if ($cart) {
            $this->checkCartQuantityForDecrement(
                $cart
            );
            $cart->decrement('quantity');
        }

        return $cart;
    }

    public function transferGuestCartToUserCart(
        string $token
    ): void {
        if ($token) {
            $carts = $this->cart::where(
                'user_id', authUser()->id
            )->get();

            if (count($carts) > 0) {
                $this->transferCartIfExists(
                    $token,
                    authUser()->id
                );
            } else {
                $this->transferCartIfNotExists(
                    $token
                );
            }
        }
    }

    public function createGuestCart(
        Product $product,
        string $token,
    ): Cart {
        $cart = $this->cart::create([
            'quantity' => 1,
            'product_id' => $product->id,
            'guest_token' => $token,
        ]);

        return $cart;
    }

    public function createUserCart(
        Product $product,
        int $userId
    ): Cart {
        $cart = $this->cart::create([
            'quantity' => 1,
            'product_id' => $product->id,
            'user_id' => $userId,
        ]);

        return $cart;
    }

    public function getTheCart(
        Product $product,
        ?string $token,
        ?int $userId,
    ): ?Cart {
        $attributes = [
            ['product_id', $product->id],
        ];

        if (Auth::check()) {
            array_push(
                $attributes,
                ['user_id', $userId],
            );
        } else {
            if ($token) {
                array_push(
                    $attributes,
                    ['guest_token', $token],
                );
            }
        }

        $cart = $this
            ->cart::where($attributes)
            ->first();

        return $cart;
    }

    public function checkProductQuantityForIncreament(
        Product $product,
        Cart $cart
    ): void {
        if (
            $product->quantity <= $cart->quantity
        ) {
            throw new Error('
                Cart Quantity can be more then product quantity
            ');
        }
    }

    public function checkCartQuantityForDecrement(
        Cart $cart,
    ): void {
        if ($cart->quantity <= 1) {
            $cart->delete();

            throw new Error(
                'Cart quantity is '.$cart->quantity
            );
        }
    }

    public function transferCartIfNotExists(
        string $token
    ): void {

        $cart_ids = $this->cart::where(
            'guest_token',
            $token
        )
            ->pluck('id')
            ->toArray();

        DB::table('carts')
            ->whereIn('id', $cart_ids)
            ->update(
                [
                    'user_id' => authUser()->id,
                    'guest_token' => null,
                ]
            );
    }

    public function transferCartIfExists(
        string $token,
        int $userId
    ): void {
        $tmp_carts = $this->cart::where(
            'guest_token',
            $token
        )->get();

        foreach ($tmp_carts as $cart) {
            $cart_item = $this->getTheCart(
                $cart->product,
                $token,
                $userId,
            );

            if ($cart_item) {
                $this->checkProductQuantityForIncreament(
                    $cart_item->product,
                    $cart_item
                );
                $cart_item->increment('quantity');
            } else {
                $this->createUserCart(
                    $cart->product->id,
                    $userId,
                );
            }

            $cart->delete();
        }
    }
}
