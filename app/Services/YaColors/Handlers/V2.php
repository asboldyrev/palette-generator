<?php

namespace App\Services\YaColors\Handlers;

use App\Services\YaColors\HandlerInterface;
use Imagick;

class V2 implements HandlerInterface
{
    public function createPalette(Imagick $image): array
    {
        dd($image);
    }
}
