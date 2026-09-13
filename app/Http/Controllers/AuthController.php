<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function registerView(): View
    {
        return view('auth.register');
    }

    public function loginView(): View
    {
        return view('auth.login');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|min:3|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:5|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $token = Cookie::get('guest_token');

        if ($token) {
            $cart_ids = Cart::where('guest_token', $token)
                ->pluck('id')
                ->toArray();

            DB::table('carts')
                ->whereIn('id', $cart_ids)
                ->update(
                    [
                        'user_id' => $user->id,
                        'guest_token' => null,
                    ]
                );
        }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $request->session()->regenerate();
            $request->session()->regenerateToken();

            return redirect()->to(route('dashboard'))->with('success', 'you are logged in');
        } else {
            return redirect()->to(route('login'))->withErrors(['auth' => 'credentials are wrong'])->withInput([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $request->session()->regenerate();
            $request->session()->regenerateToken();
            if (auth()->user()->is_admin) {
                return redirect()->to(route('filament.admin.auth.login'))->with('success', 'you are logged in');
            } else {
                $token = Cookie::get('guest_token');

                if ($token) {
                    $carts = Cart::where('user_id', auth()->user()->id)->get();

                    if (count($carts) > 0) {
                        $tmp_carts = Cart::where('guest_token', $token)->get();

                        foreach ($tmp_carts as $cart) {
                            $cart_item = Cart::where([
                                ['product_id', $cart->product->id],
                                ['user_id', auth()->user()->id],
                            ])->first();

                            if ($cart_item) {
                                $cart_item->increment('quantity');
                                $cart_item->save();

                            } else {
                                Cart::create([
                                    'product_id' => $cart->product->id,
                                    'quantity' => 1,
                                    'user_id' => auth()->user()->id,
                                ]);
                            }

                            $cart->delete();
                        }
                    } else {
                        $cart_ids = Cart::where('guest_token', $token)
                            ->pluck('id')
                            ->toArray();

                        DB::table('carts')
                            ->whereIn('id', $cart_ids)
                            ->update(
                                [
                                    'user_id' => auth()->user()->id,
                                    'guest_token' => null,
                                ]
                            );
                    }
                }

                return redirect()->to(route('dashboard'))->with('success', 'you are logged in');
            }
        } else {
            return redirect()->to(route('login'))->withErrors(['auth' => 'credentials are wrong'])->withInput([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->to(route('login'));
    }

    public function dashboard(): View
    {
        $user = User::find(auth()->user()->id);
        $addresses = Address::where('user_id', auth()->user()->id)->get();
        $orders = Order::where('user_id', auth()->user()->id)->paginate(5);

        return view('auth.dashboard', compact(
            'user',
            'addresses',
            'orders',
        ));
    }
}
