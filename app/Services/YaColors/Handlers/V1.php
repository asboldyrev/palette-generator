<?php

namespace App\Services\YaColors\Handlers;

use App\Services\YaColors\HandlerInterface;
use App\Services\YaColors\ImageProcessing\ImageFileHandler;
use App\Services\YaColors\Models\Image;
use Imagick;

class V1 implements HandlerInterface
{
    public function createPalette(Image $image, Imagick $imagick): Image
    {
        $palette = [];
        // Создаём палитру
        $filter = Imagick::FILTER_BOX;
        $palette_image = clone $imagick;
        $palette_image->resizeImage(2, 2, $filter, 0);

        $iterator = $palette_image->getPixelIterator();
        foreach ($iterator as $pixels) {
            foreach ($pixels as $pixel) {
                $color = $pixel->getColor();

                array_pop($color);

                foreach ($color as &$chanel) {
                    $chanel = str_pad(dechex($chanel), 2, '0', STR_PAD_LEFT);
                }

                $palette[] = implode('', $color);
            }
            $iterator->syncIterator();
        }

        $palette_image->resizeImage(
            $imagick->getImageWidth(),
            $imagick->getImageHeight(),
            $filter,
            0
        );

        $image_path = ImageFileHandler::saveImage($image, $palette_image, 'v1');
        $image->paths->addPalette('v1', $image_path);
        $image->setPalette('v1', $palette);

        ImageFileHandler::saveData($image);

        return $image;
    }
}
