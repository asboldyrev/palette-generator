<?php

namespace App\Services\YaColors\Handlers;

use Illuminate\Http\UploadedFile;

class V2
{
    public function __construct(UploadedFile $file)
    {
        dd($file);
    }
}
