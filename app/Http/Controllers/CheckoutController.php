<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Country;
use App\Models\District;
use App\Models\State;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(): View | JsonResponse
    {
        $countries = Country::all();
        $states = State::where("country_id", $countries[0]->id)->get();

        if(request()->ajax())
        {
            if(isset(request()->country_id))
            {
                $states = State::where("country_id", request()->country_id)->select("name", "id")->get();

                return response()->json(["data" => $states]);
            }

            if(isset(request()->state_id))
            {
                $districts = District::where("state_id", request()->state_id)->select("name", "id")->get();

                return response()->json(["data" => $districts]);
            }

        }

        return view("user.checkout", compact("countries", "states"));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            "name" => "required|min:5|max:20",
            "phone_number" => "required|digits:10",
            "country" => "required",
            "state" => "required",
            "district" => "required",
            "pin_code" => "required|digits:6",
            "address" => "required|min:10|max:100",
            "house_number" => "required",
            "city" => "required",
        ]);

        $address = Address::create([
            "name" => $request->name,
            "user_id" => auth()->user()->id,
            "country_id" => $request->country,
            "state_id" => $request->state,
            "district_id" => $request->district,
            "phone" => $request->phone_number,
            "house_number" => $request->house_number,
            "city" => $request->city,
            "address" => $request->address,
            "pin_code" => $request->pin_code,
        ]);

        $carts = Cart::join(
                "products", "carts.product_id", "=", "products.id"
            )
            ->where("user_id", auth()->user()->id)
            ->select(
                "carts.*",
                "products.price",
                DB::raw("carts.quantity * products.price as line_total")
            )
            ->get();

        $cartsTotal = $carts->sum("line_total");
        $orderId = $this->generateOrderId(6);

        $order = Order::create([
            "user_id" => auth()->user()->id,
            "address_id" => $address->id,
            "order_id" => $orderId,
            "total" => $cartsTotal,
        ]);

        foreach($carts as $cart)
        {
            OrderItem::create([
                "order_id" => $order->id,
                "product_id" => $cart->product->id,
                "price" => $cart->price,
                "quantity" => $cart->quantity,
                "amount" => $cart->line_total,
            ]);

            $product = Product::find($cart->product->id);
            $product->quantity -= (int) $cart->quantity;
            $product->save();

            $cart->delete();
        }

        return redirect()
            ->to(route("home"))
            ->with("success", "you are successfully created Order");
    }

    public function generateOrderId(int $num): int
    {
        $result = "";
        $char = "0123456789";

        for($i = 0; $i < $num; $i++)
        {
            $result .= $char[random_int(0, strlen($char)) - 1];
        }

        return (int) $result;
    }
}
