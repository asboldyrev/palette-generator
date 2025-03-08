<?php

namespace App\Services\YaColors\Handlers;

use App\Services\YaColors\HandlerInterface;
use App\Services\YaColors\ImageProcessing\ImageFileHandler;
use App\Services\YaColors\Models\Image;
use Imagick;
use ImagickPixel;
use Phpml\Clustering\KMeans;
use Phpml\Math\Statistic\Mean;
use Spatie\Color\Hsl;
use Spatie\Color\Hsv;
use Spatie\Color\Rgb;

class V4 implements HandlerInterface
{
    public function createPalette(Image $image, Imagick $imagick): Image
    {
        $numClusters = 4;
        $hsvColors = [];

        $histogram = $imagick->getImageHistogram();

        foreach ($histogram as $pixel) {
            /** @var ImagickPixel $pixel */
            $color = $pixel->getColor();
            $hsv = (new Rgb($color['r'], $color['g'], $color['b']))->toHsl();
            $hsvColors[] = [$hsv->hue(), $hsv->saturation(), $hsv->lightness()];
        }

        // Кластеризация по hue
        $kmeans = new KMeans($numClusters);
        $hueClusters = $kmeans->cluster(array_column($hsvColors, 0));

        $hueModes = [];
        foreach ($hueClusters as $cluster) {
            $hueModes[] = Mean::mode($cluster);
        }

        $saturationClusters = [];
        foreach ($hueModes as $hue) {
            $filtered = array_filter($hsvColors, fn($color) => $color[0] === $hue);
            $saturations = array_column($filtered, 1);
            $saturationClusters[] = $kmeans->cluster($saturations);
        }

        $saturationModes = [];
        foreach ($saturationClusters as $clusters) {
            foreach ($clusters as $cluster) {
                $saturationModes[] = Mean::mode($cluster);
            }
        }

        $valueClusters = [];
        foreach ($saturationModes as $sat) {
            $filtered = array_filter($hsvColors, fn($color) => $color[1] === $sat);
            $values = array_column($filtered, 2);
            $valueClusters[] = $kmeans->cluster($values);
        }

        $valueModes = [];
        foreach ($valueClusters as $clusters) {
            foreach ($clusters as $cluster) {
                $valueModes[] = Mean::mode($cluster);
            }
        }

        $palette_image = new Imagick();
        $palette_image->setFormat($image->paths->extension);
        $palette_image->newImage(intval($numClusters / 2) * 100, 200, new ImagickPixel('white'));

        $x = 0;
        $y = 0;
        $color_palette = [];

        foreach ($hueModes as $index => $hue) {
            $saturation = $saturationModes[$index] ?? 100;
            $value = $valueModes[$index] ?? 100;
            $rgb = (new Hsl($hue, $saturation, $value))->toRgb();
            $color = new ImagickPixel('rgb(' . $rgb->red() . ',' . $rgb->green() . ',' . $rgb->blue() . ')');

            if ($index > 0 && $index % round($numClusters / 2) == 0) {
                $x = 0;
                $y += 100;
            }

            $rectangle = new Imagick();
            $rectangle->newImage(100, 100, $color);
            $palette_image->compositeImage($rectangle, Imagick::COMPOSITE_OVER, $x, $y);

            $hex_color = $rgb->toHex();
            $color_palette[] = $hex_color->red() . $hex_color->green() . $hex_color->blue();

            $x += 100;
        }

        $image_path = ImageFileHandler::saveImage($image, $palette_image, 'v4');
        $image->paths->addPalette('v4', $image_path);
        $image->setPalette('v4', $color_palette);

        ImageFileHandler::saveData($image);

        return $image;
    }
}
