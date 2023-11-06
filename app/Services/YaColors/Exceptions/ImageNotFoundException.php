<?php

namespace App\Services\YaColors\Exceptions;

use Exception;

class ImageNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Изображение не найдено');
    }
}
