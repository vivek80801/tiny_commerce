<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\State;

#[Fillable(["name", "state_id"])]
class District extends Model
{
    /**
    * @return BelongsTo
    */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
