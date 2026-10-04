<?php

namespace App\Http\Controllers;

use App\CreateAddressReq;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

use function App\Helpers\authUser;
use function App\Helpers\getIdempotencyKey;

class CheckoutController extends Controller
{
    use CreateAddressReq;

    public function __construct(
        private CheckoutService $checkoutService
    ) {}

    public function index(): View|JsonResponse
    {
        return $this->getAddress('user.checkout');
    }

    public function store(
        CheckoutRequest $request
    ): RedirectResponse {
        $checkoutKey = getIdempotencyKey('checkout');
        $isCheckout = session('processed:'.$checkoutKey);
        $sessionKey = session($checkoutKey);
        $comingKey = request()->query($checkoutKey);

        if (
            $sessionKey === $comingKey &&
                ! $isCheckout
        ) {
            session([
                'processed:'.$isCheckout => true,
            ]);

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
        $buynowKey = getIdempotencyKey('buynow');
        $isBuynow = session('processed:'.$buynowKey);
        $sessionKey = session($buynowKey);
        $comingKey = request()->query($buynowKey);

        if (
            $sessionKey === $comingKey &&
                 ! $isBuynow
        ) {
            session([
                'processed:'.$isBuynow => true,
            ]);

            $this->checkoutService->buynow(
                authUser()->id, $product->id
            );
        }

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
