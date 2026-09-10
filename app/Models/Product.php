<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[Fillable(["name", "price", "description", "quantity", "category_id" ])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */

    use HasFactory;


    /**
    * @return BelongsTo
    */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
    * @return MorphOne
    */
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, "imageable");
    }
}
