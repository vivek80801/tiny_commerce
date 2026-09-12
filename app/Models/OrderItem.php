<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Order;

#[Fillable([
    "order_id",
    "product_id",
    "price",
    "quantity",
    "amount"
])]
class OrderItem extends Model
{
    /**
    * @return BelongsTo
    */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
