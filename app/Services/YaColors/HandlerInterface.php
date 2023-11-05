<?php

namespace App\Services\YaColors;

use Imagick;

interface HandlerInterface
{
    public function createPalette(Imagick $image): array;
}
