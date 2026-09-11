<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index(): View
    {
        $carts = [];
        if(Auth::check())
        {
            $carts = Cart::where("user_id", auth()->user()->id)
                ->paginate(10);
        }else {
            $token = Cookie::get("guest_token");
            if($token)
            {
                $carts = Cart::where("guest_token", $token)
                    ->paginate(10);
            }
        }
        return view("user.cart", compact("carts"));
    }

    public function addToCart(Product $product): RedirectResponse
    {
        if(Auth::check())
        {
            $cart = Cart::where([
                [ "product_id", $product->id ],
                [ "user_id", auth()->user()->id ],
            ])->first();

            if($cart)
            {
                $cart->increment("quantity");
                $cart->save();
            } else {
                Cart::create([
                    "quantity" => 1,
                    "product_id" => $product->id,
                    "user_id" => auth()->user()->id,
                ]);
            }

        } else {
            $guest_token = Cookie::get("guest_token");
            if($guest_token)
            {
                $cart = Cart::where([
                    [ "product_id", $product->id ],
                    [ "guest_token", $guest_token],
                ])->first();

                if($cart)
                {
                    $cart->increment("quantity");
                    $cart->save();
                } else {

                    Cart::create([
                        "quantity" => 1,
                        "product_id" => $product->id,
                        "guest_token" => $guest_token,
                    ]);
                }
            }else {
                $uuid = Str::uuid();
                Cookie::queue(Cookie::make("guest_token", $uuid, 60));

                Cart::create([
                    "quantity" => 1,
                    "product_id" => $product->id,
                    "guest_token" => $uuid,
                ]);
            }
        }
        return redirect()
            ->to(route("cart.index"))
            ->with("success", "Product is add to your cart");
    }

    public function increment(Product $product): RedirectResponse
    {
        if(Auth::check())
        {
            $cart = Cart::where([
                ["product_id", $product->id],
                ["user_id", auth()->user()->id],
            ])->first();

            $cart->increment("quantity");
            $cart->save();

            return redirect()->back()->with("success", "cart has been updated");
        } else {
            $token = Cookie::get("guest_token");
            if($token)
            {
                $cart = Cart::where([
                    ["product_id", $product->id],
                    ["guest_token", $token],
                ])->first();

                $cart->increment("quantity");
                $cart->save();
                return redirect()->back()->with("success", "cart has been updated");
            }
        }
        return redirect()->to(route("cart.index"))->with("error", "you don't have item in cart");
    }

    public function decrease(Product $product)
    {
        if(Auth::check())
        {
            $cart = Cart::where([
                ["product_id", $product->id],
                ["user_id", auth()->user()->id],
            ])->first();

            if($cart->quantity <= 1)
            {
                $cart->delete();

                return redirect()->back()->with("success", "product has been deleted from cart");
            }

            $cart->decrement("quantity");
            $cart->save();

            return redirect()->back()->with("success", "cart has been updated");
        }else {
            $token = Cookie::get("guest_token");

            if($token)
            {
                $cart = Cart::where([
                    ["product_id", $product->id],
                    ["guest_token", $token],
                ])->first();

                if($cart->quantity <= 1)
                {
                    $cart->delete();
                    return redirect()->back()->with("success", "product has been deleted from cart");
                }

                $cart->decrement("quantity");
                $cart->save();

                return redirect()->back()->with("success", "cart has been updated");
            }
        }
    }
}
