<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function App\Helpers\authUser;
use function App\Helpers\getGuestToken;

class CartMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cartCount = 0;
        if (authUser()) {
            $cart = Cart::where(
                'user_id', authUser()->id
            )
                ->select(
                    'quantity'
                );
            $cartCount = $cart->sum('quantity');
        } else {
            $token = getGuestToken();
            if ($token) {
                $cart = Cart::where(
                    'guest_token', $token
                )
                    ->select(
                        'quantity'
                    );

                $cartCount = $cart->sum('quantity');
            }
        }
        session(['cartCount' => $cartCount]);

        return $next($request);
    }
}
