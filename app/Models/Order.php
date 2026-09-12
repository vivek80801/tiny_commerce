<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    "user_id",
    "address_id",
    "order_id",
    "total"
])]
class Order extends Model
{
    /**
    * @return HasMany
    */
    public function orderItem (): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
    * @return BelongsTo
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
    * @return BelongsTo
    */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

}
