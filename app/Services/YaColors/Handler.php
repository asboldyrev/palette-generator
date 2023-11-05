<?php

namespace App\Services\YaColors;

use App\Services\YaColors\Handlers\AbstractHandler;
use App\Services\YaColors\Handlers\V1;
use App\Services\YaColors\Handlers\V2;
use Illuminate\Http\UploadedFile;

class Handler
{
    public static function make(UploadedFile $file, int $version = 1): AbstractHandler
    {
        if ($version == 2) {
            return V2::make($file);
        }

        return V1::make($file);
    }

    public static function load(string $version, string $id): AbstractHandler
    {
        // if ($version == 2) {
        //     return new V2($file);
        // }

        return V1::load($version, $id);
    }
}
