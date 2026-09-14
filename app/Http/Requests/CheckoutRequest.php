<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Address;

use function App\Helpers\authUser;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $address = Address::where(
            'user_id', authUser()->id
        )->first();

        if($address)
        {
            return [
                "address" => "required",
            ];
        } else {
            return [
                'name' => 'required|min:5|max:20',
                'phone_number' => 'required|digits:10',
                'country' => 'required',
                'state' => 'required',
                'district' => 'required',
                'pin_code' => 'required|digits:6',
                'address' => 'required|min:10|max:100',
                'house_number' => 'required',
                'city' => 'required',
            ];
        }
    }
}
