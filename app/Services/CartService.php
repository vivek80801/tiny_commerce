<?php

namespace App\Services;

use App\Exceptions\CartQuantityCheckException;
use App\Exceptions\CartTransferException;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function App\Helpers\authUser;

class CartService
{
    public function __construct(
        private Cart $cart,
    ) {}

    /**
     * @param  array<int, string>  $attribute
     */
    public function getCart(array $attribute): LengthAwarePaginator
    {
        $query = $this->cart::query()
            ->where(
                ...$attribute
            );

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

        return $this->getLatestCartFromQuery(
            $query
        );
    }

    public function addToCart(
        Product $product,
        ?int $userId,
        ?string $token,
    ): void {
        DB::transaction(function () use ($product, $userId, $token) {
            $cart = $this->getTheCart(
                $product,
                $token,
                $userId,
            );

            if ($cart) {

                $this->checkAndIncrementCart($product, $cart);
            } elseif ($userId) {

                $this->createUserCart($product, $userId);
            } elseif ($token) {

                $this->createGuestCart($product, $token);
            }
        });
    }

    public function increment(
        Product $product,
        ?int $userId,
        ?string $token
    ): void {
        DB::transaction(function () use ($product, $userId, $token) {
            $cart = $this->getTheCart(
                $product,
                $token,
                $userId,
            );

            if ($cart) {
                $this->checkAndIncrementCart($product, $cart);
            }
        });
    }

    public function decrement(
        Product $product,
        ?int $userId,
        ?string $token
    ): Cart {
        return DB::transaction(function () use ($product, $userId, $token) {
            $cart = $this->getTheCart(
                $product,
                $token,
                $userId,
            );

            if ($cart) {
                $this->checkAndDcrementCart($cart);
            }

            return $cart;
        });
    }

    public function transferGuestCartToUserCart(
        ?string $token,
        ?int $userId,
    ): void {
        if ($token) {
            $carts = $this->cart::where(
                'user_id', $userId
            )->get();

            if (count($carts) > 0) {
                // it is one by one transfer of cart
                $this->transferCartIfExists(
                    $token,
                    $userId
                );
            } else {
                // it is bulk transer of cart

                $this->transferCartIfNotExists(
                    $token,
                    $userId
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

    private function createUserCart(
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

    private function getTheCart(
        Product $product,
        ?string $token,
        ?int $userId,
    ): ?Cart {
        $attributes = [
            ['product_id', $product->id],
        ];

        if ($userId) {
            array_push(
                $attributes,
                ['user_id', $userId],
            );
        } elseif ($token) {
            array_push(
                $attributes,
                ['guest_token', $token],
            );
        } else {
            return null;
        }

        $cart = $this
            ->cart::where($attributes)
            ->first();

        return $cart;
    }

    private function checkProductQuantityForIncreament(
        Product $product,
        Cart $cart,
    ): bool {
        $productStock = Product::query()
            ->whereKey($product->id)
            ->lockForUpdate()
            ->first();

        if ($cart->quantity > $productStock->quantity) {
            return false;
        } else {
            return true;
        }
    }

    private function checkCartQuantityForDecrement(
        Cart $cart,
    ): bool {
        if ($cart->quantity <= 1) {
            return false;
        } else {
            return true;
        }
    }

    public function transferCartIfNotExists(
        string $token,
        int $userId,
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
                    'user_id' => $userId,
                    'guest_token' => null,
                ]
            );
    }

    private function transferCartIfExists(
        string $token,
        int $userId
    ): void {
        try {
            DB::beginTransaction();
            $temCarts = $this->cart::where(
                'guest_token',
                $token
            )->get();

            foreach ($temCarts as $cart) {
                $cartItem = $this->cart::where(
                    'product_id',
                    $cart->product_id
                )
                    ->where('user_id', $userId)
                    ->lockForUpdate()
                    ->first();

                if ($cartItem) {
                    $this->checkAndIncrementCart(
                        $cartItem->product,
                        $cartItem,
                    );

                } else {
                    $this->createUserCart(
                        $cart->product,
                        $userId,
                    );
                }

                $cart->delete();
            }
            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error(
                'user.id: '.authUser()->id."\n".
                $e->getMessage()
            );
            throw new CartTransferException('
                Cart Transfer Failed
            ');
        }
    }

    private function checkAndIncrementCart(
        Product $product,
        Cart $cart,
    ) {
        $isProductQuantityGood = $this->checkProductQuantityForIncreament(
            $product,
            $cart,
        );

        $this->incrementCart($isProductQuantityGood, $cart);
    }

    private function incrementCart(
        bool $isProductQuantityGood,
        Cart $cart,
    ): void {
        if ($isProductQuantityGood) {
            $cart->increment('quantity');
        } else {
            throw new CartQuantityCheckException('
                Product quantity in the cart can not be greater then product stock
                ');
        }
    }

    private function checkAndDcrementCart(
        Cart $cart,
    ) {
        $isCartQuantityGood = $this->checkCartQuantityForDecrement(
            $cart
        );

        $this->decrementCart($isCartQuantityGood, $cart);
    }

    private function decrementCart(
        bool $isCartQuantityGood,
        Cart $cart,
    ) {
        if ($isCartQuantityGood) {
            $cart->decrement('quantity');
        } else {

            $cart->delete();
        }
    }

    public function getLatestCartFromQuery(
        Builder $query
    ): LengthAwarePaginator {
        return $query
            ->orderByDesc('created_at')
            ->paginate(10);
    }
}
