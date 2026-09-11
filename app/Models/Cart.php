<?php

namespace App\Models;

use Database\Factories\CartFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(["quantity", "product_id", "user_id", "guest_token"])]
class Cart extends Model
{
    /** @use HasFactory<CartFactory> */

    use HasFactory;

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
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
