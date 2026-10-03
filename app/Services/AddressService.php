<?php

namespace App\Services;

use App\Models\Address;

class AddressService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private Address $address
    ) {}

    /**
     * @param  array<string, mixed>  $newAddress
     */
    public function createAddressForCheckout(
        array $newAddress,
        int $userId,
    ): Address {
        $address = $this->address::where(
            'user_id', $userId
        )->first();
        if ($address) {
            $address = $this->address::find(
                (int) $newAddress['address']
            );
        } else {
            $address = $this->create(
                $newAddress,
                $userId,
            );
        }

        return $address;
    }

    /**
     * @param  array<string, mixed>  $newAddress
     */
    public function create(
        array $newAddress,
        int $userId,
    ): Address {
        $address = $this->address::create([
            'name' => $newAddress['name'],
            'user_id' => $userId,
            'country_id' => $newAddress['country'],
            'state_id' => $newAddress['state'],
            'district_id' => $newAddress['district'],
            'phone' => $newAddress['phone_number'],
            'house_number' => $newAddress['house_number'],
            'city' => $newAddress['city'],
            'address' => $newAddress['address'],
            'pin_code' => $newAddress['pin_code'],
        ]);

        return $address;
    }
}
