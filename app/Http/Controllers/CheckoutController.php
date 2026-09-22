<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Address;
use App\Models\Country;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\State;
use App\Services\CheckoutService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

use function App\Helpers\authUser;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService
    ) {}

    public function index(): View|JsonResponse
    {
        $countries = Country::all();
        $states = State::where(
            'country_id',
            $countries[0]->id
        )->get();

        $addresses = Address::where(
            'user_id', authUser()->id
        )->get();

        if (request()->ajax()) {
            if (isset(request()->country_id)) {
                $states = State::where(
                    'country_id',
                    request()->country_id
                )
                    ->select('name', 'id')->get();

                return response()
                    ->json(
                        ['data' => $states]
                    );
            }

            if (isset(request()->state_id)) {
                $districts = District::where(
                    'state_id', request()->state_id
                )->select('name', 'id')
                    ->get();

                return response()
                    ->json(
                        ['data' => $districts]
                    );
            }

        }

        return view(
            'user.checkout',
            compact(
                'countries',
                'states',
                'addresses',
            )
        );
    }

    public function store(
        CheckoutRequest $request
    ): RedirectResponse {
        try {
            $this->checkoutService->createOrder(
                $request->all(),
                authUser()->id
            );
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return redirect()
                ->to(route('home'))
                ->with(
                    'error',
                    'There is an issue when creating order'
                );
        }

        return redirect()
            ->to(route('home'))
            ->with(
                'success',
                'you are successfully created Order'
            );
    }

    public function buynow(
        Product $product
    ): RedirectResponse {
        $this->checkoutService->buynow(
            authUser()->id, $product->id
        );

        return redirect()->to(route('checkout'));
    }

    public function orderDetail(
        Order $order
    ): View {
        $orderDetails = OrderItem::where(
            'order_id', $order->id
        )->get();

        return view(
            'user.order_detail',
            compact('orderDetails')
        );
    }
}
