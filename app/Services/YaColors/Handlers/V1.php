<?php

namespace App\Services\YaColors\Handlers;

use Illuminate\Http\UploadedFile;
use Imagick;

class V1 extends AbstractHandler
{
    /**
     * @var Imagick
     */
    protected $originalImage;

    public static function make(UploadedFile $file)
    {
        return parent::create($file, 'v1');
    }

    protected function createPalette(Imagick $image): Imagick
    {
        // Создаём палитру
        $filter = Imagick::FILTER_BOX;
        $palette_image = clone $image;
        $palette_image->resizeImage(2, 2, $filter, 0);

        $iterator = $palette_image->getPixelIterator();
        foreach ($iterator as $row => $pixels) {
            foreach ($pixels as $col => $pixel) {
                $color = $pixel->getColor();

                array_pop($color);

                foreach ($color as &$chanel) {
                    $chanel = str_pad(dechex($chanel), 2, '0', STR_PAD_LEFT);
                }

                $this->palette[] = implode('', $color);
            }
            $iterator->syncIterator();
        }

        $palette_image->resizeImage(
            $image->getImageWidth(),
            $image->getImageHeight(),
            $filter,
            0
        );

        $this->saveImage($palette_image, 'resize');

        return $palette_image;
    }
}
