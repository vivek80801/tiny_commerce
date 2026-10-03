<?php

namespace App;

use App\Models\Address;
use App\Models\Country;
use App\Models\District;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

use function App\Helpers\authUser;

trait CreateAddressReq
{
    public function getAddress(
        string $view
    ): View|JsonResponse {
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
            $view,
            compact(
                'countries',
                'states',
                'addresses',
            )
        );
    }
}
