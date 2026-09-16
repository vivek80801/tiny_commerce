<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

use function App\Helpers\getGuestToken;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index(): View
    {
        $carts = $this->cartService->getCart();
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
        try {
            $this
                ->cartService
                ->addToCart($product);
        } catch (Throwable $e) {
            Log::error($e->getMessage());

            return redirect()
                ->to(route('cart.index'))
                ->with(
                    'error',
                    $e->getMessage()
                );
        }

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
                ->increment($product);
        } catch (Throwable $e) {
            Log::error($e->getMessage());

            return redirect()
                ->back()
                ->with(
                    'error',
                    $e->getMessage()
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
                ->decrement($product);
        } catch (Throwable $e) {
            Log::error($e->getMessage());

            return redirect()
                ->back()
                ->with(
                    'success',
                    'product has been deleted from cart'
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
