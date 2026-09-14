<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Country;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\State;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

use function App\Helpers\generateOrderId;
use function App\Helpers\authUser;

class CheckoutController extends Controller
{
    public function index(): View|JsonResponse
    {
        $countries = Country::all();
        $states = State::where('country_id', $countries[0]->id)->get();
        $addresses = Address::where('user_id', authUser()->id)->get();

        if (request()->ajax()) {
            if (isset(request()->country_id)) {
                $states = State::where(
                    'country_id', request()->country_id
                )
                    ->select('name', 'id')->get();

                return response()->json(['data' => $states]);
            }

            if (isset(request()->state_id)) {
                $districts = District::where(
                    'state_id', request()->state_id
                )->select('name', 'id')
                    ->get();

                return response()->json(['data' => $districts]);
            }

        }

        return view('user.checkout', compact('countries', 'states', 'addresses'));
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $address = Address::where('user_id', authUser()->id)->first();

        DB::beginTransaction();
        try{
            if ($address) {
                $address = Address::find(
                    (int) $request->address
                );
            } else {
                $address = Address::create([
                    'name' => $request->name,
                    'user_id' => authUser()->id,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'district_id' => $request->district,
                    'phone' => $request->phone_number,
                    'house_number' => $request->house_number,
                    'city' => $request->city,
                    'address' => $request->address,
                    'pin_code' => $request->pin_code,
                ]);
            }

            $carts = Cart::join(
                'products', 'carts.product_id', '=', 'products.id'
            )
                ->where('user_id', authUser()->id)
                ->select(
                    'carts.*',
                    'products.price',
                    DB::raw('carts.quantity * products.price as line_total')
                )
                ->get();

            $cartsTotal = $carts->sum('line_total');
            $orderId = generateOrderId(6);

            $order = Order::create([
                'user_id' => authUser()->id,
                'address_id' => $address->id,
                'order_id' => $orderId,
                'total' => $cartsTotal,
            ]);

            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product->id,
                    'price' => $cart->price,
                    'quantity' => $cart->quantity,
                    'amount' => $cart->line_total,
                ]);

                $product = Product::find($cart->product->id);
                $product->quantity -= (int) $cart->quantity;
                $product->save();

                $cart->delete();
            }
        }catch(Throwable $e) {
            DB::rollBack();
            Log::error($e);
        }

        return redirect()
            ->to(route('home'))
            ->with('success', 'you are successfully created Order');
    }

    public function buynow(Product $product): RedirectResponse
    {
        $attributes = [
            'user_id' => authUser()->id,
            'product_id' => $product->id,
        ];
        $cart = Cart::where($attributes)->first();

        if ($cart) {
            $cart->increment('quantity');
        } else {
            Cart::create([
                'quantity' => 1,
                ...$attributes,
            ]);
        }

        return redirect()->to(route('checkout'));
    }

    public function orderDetail(Order $order): View
    {
        $orderDetails = OrderItem::where('order_id', $order->id)->get();

        return view('user.order_detail', compact('orderDetails'));
    }
}
