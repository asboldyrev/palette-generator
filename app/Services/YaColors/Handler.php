<?php

namespace App\Services\YaColors;

use App\Services\YaColors\Handlers\V1;
use Illuminate\Http\UploadedFile;

class Handler
{
    public static function make(UploadedFile $file, int $version = 1)
    {
        if ($version == 2) {
            //
        }

        return new V1($file);
    }
}
