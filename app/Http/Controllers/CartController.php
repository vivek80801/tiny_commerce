<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

use function App\Helpers\getGuestToken;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index(): View
    {
        $carts = $this->cartService->getCart();

        return view('user.cart', compact('carts'));
    }

    public function addToCart(Product $product): RedirectResponse
    {
        $this->cartService->addToCart($product);

        return redirect()
            ->to(route('cart.index'))
            ->with('success', 'Product is add to your cart');
    }

    public function increment(Product $product): RedirectResponse
    {
        $token = getGuestToken();

        if (! Auth::check() && ! $token) {
            return redirect()
                ->to(route('cart.index'))
                ->with('error', "you don't have item in cart");
        }

        $this->cartService->increment($product);

        return redirect()->back()->with('success', 'cart has been updated');

    }

    public function decrement(Product $product): RedirectResponse
    {
        $token = getGuestToken();

        if (! Auth::check() && ! $token) {
            return redirect()
                ->to(route('cart.index'))
                ->with('error', "you don't have item in cart");
        }

        $cart = $this->cartService->decrement($product);

        if ($cart->quantity <= 1) {

            return redirect()->back()->with('success', 'product has been deleted from cart');
        }

        return redirect()->back()->with('success', 'cart has been updated');
    }
}
