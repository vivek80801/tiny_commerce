<?php

namespace Database\Factories;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * @extends Factory<Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dir = storage_path('app/public/uploads');

        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $width = 800;
        $height = 600;

        // Random RGB
        $r = random_int(0, 255);
        $g = random_int(0, 255);
        $b = random_int(0, 255);

        $img = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($img, $r, $g, $b);
        imagefill($img, 0, 0, $bg);

        $filename = Str::random(12).'.jpg';
        imagejpeg($img, "$dir/$filename", 90);
        imagedestroy($img);

        return [
            'filename' => $filename,
        ];
    }
}
