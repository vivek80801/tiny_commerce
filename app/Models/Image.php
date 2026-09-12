<?php

namespace App\Models;

use Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['filename'])]
class Image extends Model
{
    /** @use HasFactory<ImageFactory> */
    use HasFactory;

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
