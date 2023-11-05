<?php

use Spatie\Color\Hex;

if (! function_exists('light_background')) {
    function light_background(string $hexColor)
    {
        return Hex::fromString('#' . $hexColor)->toHsl()->lightness() > 50;
    }
}
