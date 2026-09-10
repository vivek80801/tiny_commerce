<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(["filename"])]
class Image extends Model
{
    /** @use HasFactory<ImageFactory> */
    use HasFactory;

    /**
    * @return MorphTo
    */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
