<?php

namespace App\Services\YaColors;

use App\Services\YaColors\Models\Image;
use Imagick;

interface HandlerInterface
{
    public function createPalette(Image $image, Imagick $imagick): Image;
}
