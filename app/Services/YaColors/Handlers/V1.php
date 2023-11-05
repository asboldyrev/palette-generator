<?php

namespace App\Services\YaColors\Handlers;

use App\Services\YaColors\HandlerInterface;
use Imagick;

class V1 implements HandlerInterface
{
    public function createPalette(Imagick $image): array
    {
        $palette = [];
        // Создаём палитру
        $filter = Imagick::FILTER_BOX;
        $palette_image = clone $image;
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
            $image->getImageWidth(),
            $image->getImageHeight(),
            $filter,
            0
        );

        return [
            'image' => $palette_image,
            'palette' => $palette,
        ];
    }
}
