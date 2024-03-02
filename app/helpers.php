<?php

use Spatie\Color\Hex;

if (!function_exists('light_background')) {
    function light_background(string $hexColor)
    {
        return Hex::fromString('#' . $hexColor)->toCIELab()->l() >= 50;
    }
}

if (!function_exists('map')) {
    function map($value, $fromLow, $fromHigh, $toLow, $toHigh)
    {
        $fromRange = $fromHigh - $fromLow;
        $toRange = $toHigh - $toLow;
        $scaleFactor = $toRange / $fromRange;

        // Re-zero the value within the from range
        $tmpValue = $value - $fromLow;
        // Rescale the value to the to range
        $tmpValue *= $scaleFactor;

        // Re-zero back to the to range
        return $tmpValue + $toLow;
    }
}
