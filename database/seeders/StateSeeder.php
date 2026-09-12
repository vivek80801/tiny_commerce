<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $id = Country::where("name", "India")->first()->id;
        $states = [
            [ "name" => "Andhra Pradesh", "country_id" => $id ],
            [ "name" => "Arunachal Pradesh", "country_id" => $id ],
            [ "name" => "Assam", "country_id" => $id ],
            [ "name" => "Bihar", "country_id" => $id ],
            [ "name" => "Chhattisgarh", "country_id" => $id ],
            [ "name" => "Goa", "country_id" => $id ],
            [ "name" => "Gujarat", "country_id" => $id ],
            [ "name" => "Haryana", "country_id" => $id ],
            [ "name" => "Himachal Pradesh", "country_id" => $id ],
            [ "name" => "Jharkhand", "country_id" => $id ],
            [ "name" => "Karnataka", "country_id" => $id ],
            [ "name" => "Kerala", "country_id" => $id ],
            [ "name" => "Madhya Pradesh", "country_id" => $id ],
            [ "name" => "Maharashtra", "country_id" => $id ],
            [ "name" => "Manipur", "country_id" => $id ],
            [ "name" => "Meghalaya", "country_id" => $id ],
            [ "name" => "Mizoram", "country_id" => $id ],
            [ "name" => "Nagaland", "country_id" => $id ],
            [ "name" => "Odisha", "country_id" => $id ],
            [ "name" => "Punjab", "country_id" => $id ],
            [ "name" => "Rajasthan", "country_id" => $id ],
            [ "name" => "Sikkim", "country_id" => $id ],
            [ "name" => "Tamil Nadu", "country_id" => $id ],
            [ "name" => "Telangana", "country_id" => $id ],
            [ "name" => "Tripura", "country_id" => $id ],
            [ "name" => "Uttar Pradesh", "country_id" => $id ],
            [ "name" => "Uttarakhand", "country_id" => $id ],
            [ "name" => "West Bengal", "country_id" => $id ],
            [ "name" => "Andaman and Nicobar Islands", "country_id" => $id ],
            [ "name" => "Chandigarh", "country_id" => $id ],
            [ "name" => "Dadra and Nagar Haveli and Daman and Diu", "country_id" => $id ],
            [ "name" => "Delhi", "country_id" => $id ],
            [ "name" => "Jammu and Kashmir", "country_id" => $id ],
            [ "name" => "Ladakh", "country_id" => $id ],
            [ "name" => "Lakshadweep", "country_id" => $id ],
            [ "name" => "Puducherry", "country_id" => $id ],

        ];

        foreach($states as $state)
        {
            State::firstOrCreate([
                "name" => $state["name"],
                "country_id" => $state["country_id"],
            ]);
        }
    }
}
