<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Product;
use App\Models\Image;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[Fillable(['name'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    /**
    * @return HasMany
    */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
    * @return MorphOne
    */
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, "imageable");
    }
}
