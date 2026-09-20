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
                'cartsSum'
            )
        );
    }

    public function addToCart(
        Product $product
    ): RedirectResponse {
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

        return redirect()
            ->to(route('cart.index'))
            ->with(
                'success',
                'Product is add to your cart'
            );
    }

    public function increment(
        Product $product
    ): RedirectResponse {
        $token = getGuestToken();
        $userId = Auth::check() ? authUser()->id : null;

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

        return redirect()
            ->back()
            ->with(
                'success',
                'cart has been updated'
            );
    }
}
