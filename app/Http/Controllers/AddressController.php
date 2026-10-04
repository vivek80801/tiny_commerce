<?php

namespace App\Http\Controllers;

use App\CreateAddressReq;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function App\Helpers\authUser;
use function App\Helpers\getIdempotencyKey;

class AddressController extends Controller
{
    use CreateAddressReq;

    public function __construct(
        private AddressService $addressService
    ) {}

    public function index(): View
    {
        return view('user.address.index');
    }

    public function create(): View|JsonResponse
    {
        return $this->getAddress('user.address.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $addressKey = getIdempotencyKey('address');
        $isAddress = session('processed:'.$addressKey);
        $sessionKey = session($addressKey);
        $comingKey = request()->query($addressKey);

        if (
            $sessionKey === $comingKey &&
                ! $isAddress
        ) {
            session([
                'processed:'.$isAddress => true,
            ]);

            $request->validate([
                'name' => 'required|min:5|max:20',
                'phone_number' => 'required|digits:10',
                'country' => 'required',
                'state' => 'required',
                'district' => 'required',
                'pin_code' => 'required|digits:6',
                'address' => 'required|min:10|max:100',
                'house_number' => 'required',
                'city' => 'required',
            ]);

            $this
                ->addressService
                ->create(
                    $request->all(),
                    authUser()->id,
                );

        }

        return redirect()->to(
            route('checkout')
        );
    }
}
