<?php

namespace App\Services\YaColors\ImageProcessing;

use App\Services\YaColors\HandlerInterface;
use App\Services\YaColors\Handlers\V1;
use App\Services\YaColors\Handlers\V2;
use App\Services\YaColors\Models\Image;
use Imagick;
use ImagickPixel;

class PaletteCreator
{
    public static function createPalette(Image $image, Imagick $imagick): Image
    {
        $handlers = [
            V1::class,
            // V2::class,
        ];

        foreach ($handlers as $class_name) {
            $handler = new $class_name();

            if ($handler instanceof HandlerInterface) {
                $handler->createPalette($image, $imagick);
            }
        }

        return $image;
    }

    public static function cleanImage(Imagick $imagick): Imagick
    {
        $newPixels = [];

        $iterator = $imagick->getPixelIterator();
        foreach ($iterator as $pixels) {
            /** @var ImagickPixel $pixel */
            foreach ($pixels as $pixel) {
                $color = $pixel->getColor();      // values are 0-255
                $alpha = $pixel->getColor(true);  // values are 0.0-1.0

                $r = $color['r'];
                $g = $color['g'];
                $b = $color['b'];
                $a = $alpha['a'];

                $l = $pixel->getHSL()['luminosity'];

                if ($l != 0 && $l != 1 && (int) $a != 0) {
                    $newPixels[] = $r;
                    $newPixels[] = $g;
                    $newPixels[] = $b;
                }
            }
            $iterator->syncIterator();
        }

        $pixelsCount = count($newPixels) / 3;
        $sideLength = ceil(sqrt($pixelsCount));
        $newWidth = $newHeight = $sideLength;

        if ($newHeight * $newWidth != $pixelsCount) {
            $newPixels = array_pad($newPixels, $newHeight * $newWidth * 3, 0);
        }

        $cleaned_image = new Imagick();
        $cleaned_image->newImage($newWidth, $newHeight, new ImagickPixel('black'));
        $cleaned_image->setImageFormat($imagick->getImageFormat());
        $cleaned_image->importImagePixels(0, 0, $newWidth, $newHeight, 'RGB', Imagick::PIXEL_CHAR, $newPixels);

        return $cleaned_image;
    }
}
