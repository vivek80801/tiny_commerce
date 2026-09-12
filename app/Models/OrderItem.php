<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
    * @return BelongsTo
    */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
