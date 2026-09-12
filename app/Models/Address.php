<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Country;
use App\Models\State;
use App\Models\District;

#[Fillable([
    "name",
    "phone",
    "pin_code",
    "user_id",
    "house_number",
    "city",
    "address",
    "country_id",
    "state_id",
    "district_id"
])]
class Address extends Model
{
    /**
    * @return BelongsTo
    */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
    * @return BelongsTo
    */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
    * @return BelongsTo
    */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
