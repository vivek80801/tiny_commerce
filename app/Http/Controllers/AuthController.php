<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

use function App\Helpers\authUser;
use function App\Helpers\getGuestToken;

class AuthController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function registerView(): View
    {
        return view('auth.register');
    }

    public function loginView(): View
    {
        return view('auth.login');
    }

    public function register(
        Request $request
    ): RedirectResponse {
        $request->validate([
            'name' => 'required|min:3|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:5|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $token = getGuestToken();

        if ($token) {
            try {
                $this
                    ->cartService
                    ->transferCartIfNotExists(
                        $token
                    );
            } catch (Throwable $e) {
                Log::error($e->getMessage());
            }
        }

        if (
            Auth::attempt([
                'email' => $request->email,
                'password' => $request->password,
            ])
        ) {
            $request->session()->regenerate();
            $request->session()->regenerateToken();

            return redirect()
                ->to(
                    route('dashboard')
                )
                ->with(
                    'success',
                    'you are logged in'
                );
        } else {
            return redirect()
                ->to(route('login'))
                ->withErrors(
                    [
                        'auth' => 'credentials are wrong',
                    ]
                )
                ->withInput([
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

        if (
            Auth::attempt([
                'email' => $request->email,
                'password' => $request->password,
            ])
        ) {
            $request->session()->regenerate();
            $request->session()->regenerateToken();
            if (
                auth()->user()->is_admin
            ) {
                return redirect()
                    ->to(
                        route(
                            'filament.admin.auth.login'
                        )
                    )
                    ->with(
                        'success',
                        'you are logged in'
                    );
            } else {
                $token = getGuestToken();

                $this
                    ->cartService
                    ->transferGuestCartToUserCart(
                        $token
                    );

                return redirect()
                    ->to(route('dashboard'))
                    ->with('success', 'you are logged in');
            }
        } else {
            return redirect()
                ->to(route('login'))
                ->withErrors(
                    ['auth' => 'credentials are wrong']
                )
                ->withInput([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);
        }
    }

    public function logout(
        Request $request
    ): RedirectResponse {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->to(route('login'));
    }

    public function dashboard(): View
    {
        $addresses = Address::select(
            'name',
            'phone',
            'house_number',
            'city',
            'pin_code',
            'address',
            'country_id',
            'state_id',
            'district_id',
        )
            ->where(
                'user_id',
                authUser()->id,
            )
            ->with('country:id,name')
            ->with('state:id,name')
            ->with('district:id,name')
            ->get();

        $orders = Order::select(
            'id',
            'order_id',
            'total',
            'address_id',
        )
            ->where(
                'user_id',
                authUser()->id
            )
            ->with('address:id,address')
            ->orderByDesc('created_at')
            ->paginate(5);

        return view('auth.dashboard', compact(
            'addresses',
            'orders',
        ));
    }
}
