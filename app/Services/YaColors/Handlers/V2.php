<?php

namespace App\Services\YaColors\Handlers;

use App\Services\YaColors\HandlerInterface;
use Imagick;
use ImagickDraw;
use ImagickPixel;
use Spatie\Color\Hsl;
use Spatie\Color\Rgb;

class V2 implements HandlerInterface
{
    public function createPalette(Imagick $image): array
    {
        $colors = [];

        $iterator = $image->getPixelIterator();
        foreach ($iterator as $pixels) {
            /** @var ImagickPixel $pixel */
            foreach ($pixels as $pixel) {
                $color = $pixel->getColor();
                $color = new Rgb($color['r'], $color['g'], $color['b']);
                $color = $color->toHsl();

                $hue = $color->hue();
                $saturation = round($color->saturation());
                $hue = round($hue);
                if (
                    array_key_exists($hue, $colors) &&
                    array_key_exists($saturation, $colors[$hue])
                ) {
                    $colors[$hue][$saturation][] = $color;
                } else {
                    $colors[$hue] = [
                        $saturation => [
                            $color,
                        ],
                    ];
                }
            }
            $iterator->syncIterator();
        }

        $width = 360 * 5;
        $height = 100 * 5;

        $palette_image = new Imagick();
        $palette_image->newImage($width, $height, new ImagickPixel('black'));
        $palette_image->setImageFormat($image->getImageFormat());

        for ($hue = 0; $hue < 360; $hue++) {
            if (array_key_exists($hue, $colors)) {
                for ($saturation = 0; $saturation < 100; $saturation++) {
                    if (array_key_exists($saturation, $colors[$hue])) {
                        foreach ($colors[$hue][$saturation] as $hsl) {
                            $hsl = new Hsl($hue, $saturation, 50);
                            $draw = new ImagickDraw();
                            $draw->setFillColor(new ImagickPixel($hsl->toRgb()));
                            $draw->setStrokeWidth(0);
                            $x1 = $hue * 5;
                            $y1 = $saturation * 5;
                            $x2 = $x1 + 4;
                            $y2 = $y1 + 4;
                            $draw->rectangle($x1, $y1, $x2, $y2);
                            $palette_image->drawImage($draw);
                        }
                    }
                }
            }
        }

        $palette_image->flipImage();

        return [
            'image' => $palette_image,
            'palette' => [],
        ];
    }
}
