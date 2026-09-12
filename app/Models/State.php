<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Country;
use App\Models\District;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(["name", "country_id"])]
class State extends Model
{
    /**
    * @return BelongsTo
    */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
    * @return HasMany
    */
    public function district(): HasMany
    {
        return $this->hasMany(District::class);
    }
}
