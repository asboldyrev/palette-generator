<?php

namespace App\Services\YaColors\Traits;

trait GetterSetter
{
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        return null;
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        }
    }
}
