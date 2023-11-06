<?php

namespace App\Services\YaColors\Exceptions;

use Exception;

class ModelNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Модель не найдена');
    }
}
