<?php

namespace App\Services\YaColors\Handlers;

use App\Services\YaColors\HandlerInterface;
use App\Services\YaColors\ImageProcessing\ImageFileHandler;
use App\Services\YaColors\Models\Image;
use Imagick;
use ImagickPixel;
use Phpml\Clustering\KMeans;
use Phpml\Math\Statistic\Mean;
use Spatie\Color\Rgb;

class V3 implements HandlerInterface
{
    public function createPalette(Image $image, Imagick $imagick): Image
    {
        $numClusters = 4;
        $colors = [];

        $histogram = $imagick->getImageHistogram();

        foreach ($histogram as $pixel) {
            /** @var ImagickPixel $pixel */
            $color = $pixel->getColor();
            $colors[] = [$color['r'], $color['g'], $color['b']];
        }

        // Кластеризация цветов с использованием k-средних из php-ml
        $kmeans = new KMeans($numClusters);
        $clusters = $kmeans->cluster($colors);

        $centroids = [];
        foreach ($clusters as $clusterColors) {
            $centroid = [
                'r' => Mean::mode(array_column($clusterColors, 0)),
                'g' => Mean::mode(array_column($clusterColors, 1)),
                'b' => Mean::mode(array_column($clusterColors, 2)),
            ];
            $centroids[] = $centroid;
        }

        $palette_image = new Imagick();
        $palette_image->setFormat($image->paths->extension);

        // Создаем новое изображение
        $palette_image->newImage(intval($numClusters / 2) * 100, 200, new ImagickPixel('white'));

        $x = 0;
        $y = 0;
        $color_palette = [];
        foreach ($centroids as $index => $centroid) {
            $color = new ImagickPixel('rgb(' . join(',', $centroid) . ')');

            if ($index > 0 && $index % round($numClusters / 2) == 0) {
                $x = 0;
                $y += 100;
            }

            $rectangle = new Imagick();
            $rectangle->newImage(100, 100, $color);
            $palette_image->compositeImage($rectangle, Imagick::COMPOSITE_OVER, $x, $y);
            $hex_color = (new Rgb($centroid['r'], $centroid['g'], $centroid['b']))->toHex();
            $color_palette[] = $hex_color->red() . $hex_color->green() . $hex_color->blue();

            $x += 100;
        }
        $image_path = ImageFileHandler::saveImage($image, $palette_image, 'v3');
        $image->paths->addPalette('v3', $image_path);
        $image->setPalette('v3', $color_palette);

        ImageFileHandler::saveData($image);

        return $image;
    }
}
