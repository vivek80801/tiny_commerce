<?php

namespace App\Http\Controllers;

use App\Exceptions\CartQuantityCheckException;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

use function App\Helpers\authUser;
use function App\Helpers\createGuestToken;
use function App\Helpers\getGuestToken;
use function App\Helpers\getIdempotencyKey;
use function App\Helpers\setIdempontencyKey;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index(): View
    {
        $token = getGuestToken();
        $attribute = [];
        $carts = [];
        $cartsSum = 0;
        $incrementKey = setIdempontencyKey(
            'cart-increment'
        );
        $decrementKey = setIdempontencyKey(
            'cart-decrement'
        );

        if (! Auth::check() && ! $token) {
            return view(
                'user.cart',
                compact(
                    'carts',
                    'cartsSum'
                )
            );
        }

        if (Auth::check()) {
            $attribute = [
                'user_id',
                authUser()->id,
            ];
        } else {
            if ($token) {
                $attribute = [
                    'guest_token', $token,
                ];
            }
        }

        $carts = $this->cartService->getCart(
            $attribute
        );
        $cartsSum = $carts->sum('lineTotal');

        return view(
            'user.cart',
            compact(
                'carts',
                'cartsSum',
                'incrementKey',
                'decrementKey',
            )
        );
    }

    public function addToCart(
        Product $product
    ): RedirectResponse {
        $cartKey = getIdempotencyKey('cart');
        $isAlreadyAddToCart = session('processed:'.$cartKey);
        $sessionKey = session($cartKey);
        $comingKey = request()->query($cartKey);

        if (
            $sessionKey === $comingKey &&
                ! $isAlreadyAddToCart
        ) {
            $token = getGuestToken();
            $userId = Auth::check() ? authUser()->id : null;

            if (! Auth::check() && ! $token) {
                $uuid = createGuestToken();

                $this->cartService->createGuestCart(
                    $product,
                    $uuid,
                );

                return redirect()
                    ->to(route('cart.index'))
                    ->with(
                        'success',
                        'Product is add to your cart'
                    );
            }

            $this->cartService->addToCart(
                $product,
                $userId,
                $token,
            );

            session([
                'processed:'.$cartKey => true,
            ]);

            return redirect()
                ->to(route('cart.index'))
                ->with(
                    'success',
                    'Product is add to your cart'
                );
        }

        return redirect()->back();
    }

    public function increment(
        Product $product
    ): RedirectResponse {
        $token = getGuestToken();
        $userId = Auth::check() ? authUser()->id : null;
        $cartIncrementKey = getIdempotencyKey('cart-increment');
        $isCartIncremented = session('processed:'.$cartIncrementKey);
        $sessionKey = session($cartIncrementKey);
        $comingKey = request()->query($cartIncrementKey);

        if (
            $sessionKey === $comingKey &&
                ! $isCartIncremented
        ) {
            session([
                'processed:'.$cartIncrementKey => true,
            ]);

            if (! Auth::check() && ! $token) {
                return redirect()
                    ->to(route('cart.index'))
                    ->with(
                        'error',
                        "you don't have item in cart"
                    );
            }

            try {
                $this->cartService
                    ->increment(
                        $product,
                        $userId,
                        $token
                    );

            } catch (CartQuantityCheckException $e) {
                Log::error($e->getMessage());

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        $e->getMessage()
                    );
            } catch (Throwable $e) {
                Log::error($e->getMessage());

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'something went wrong'
                    );
            }
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'cart has been updated'
            );

    }

    public function decrement(
        Product $product
    ): RedirectResponse {
        $token = getGuestToken();
        $userId = Auth::check() ? authUser()->id : null;
        $cartDecrementKey = getIdempotencyKey('cart-decrement');
        $isCartDecrement = session('processed:'.$cartDecrementKey);
        $sessionKey = session($cartDecrementKey);
        $comingKey = request()->query($cartDecrementKey);

        if (
            $sessionKey === $comingKey &&
                ! $isCartDecrement
        ) {
            session([
                'processed:'.$cartDecrementKey => true,
            ]);
            if (! Auth::check() && ! $token) {
                return redirect()
                    ->to(
                        route('cart.index')
                    )
                    ->with(
                        'error',
                        "you don't have item in cart"
                    );
            }

            try {

                $this
                    ->cartService
                    ->decrement(
                        $product,
                        $userId,
                        $token,
                    );
            } catch (Throwable $e) {
                Log::error($e->getMessage());

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Something went wrong'
                    );
            }

        }

        return redirect()
            ->back()
            ->with(
                'success',
                'cart has been updated'
            );
    }
}
