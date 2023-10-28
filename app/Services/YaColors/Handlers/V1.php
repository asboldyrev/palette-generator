<?php

namespace App\Services\YaColors\Handlers;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;
use ImagickPixel;

class V1
{
    protected string $filename;

    protected $palette;

    protected array $images;

    /**
     * @var Imagick
     */
    protected $originalImage;

    public function __construct(UploadedFile $file)
    {
        $stored_file = $file->move(storage_path('ya-colors/v1'), $file->getClientOriginalName());
        $this->filename = $stored_file->getFilename();

        $original_image = new Imagick($stored_file->getPathname());

        $this->saveFile($original_image, 'original');
        $cleaned_image = $this->cleanImage($original_image);

        return $this;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function getPalette()
    {
        return $this->palette;
    }

    protected function cleanImage(Imagick $image)
    {
        $colors = [];
        $iterator = $image->getPixelIterator();
        foreach ($iterator as $row => $pixels) {
            /** @var ImagickPixel $pixel */
            foreach ($pixels as $col => $pixel) {
                $color = $pixel->getColor();      // values are 0-255
                $alpha = $pixel->getColor(true);  // values are 0.0-1.0

                $r = $color['r'];
                $g = $color['g'];
                $b = $color['b'];
                $a = $alpha['a'];

                $l = $pixel->getHSL()['luminosity'];

                if ($l != 0 && $l != 1 && (int) $a != 0) {
                    $colors[] = compact('r', 'g', 'b', 'a');
                }
            }
            $iterator->syncIterator();
        }

        $width = (int) (sqrt(count($colors)));
        $height = $width;

        if (sqrt(count($colors)) != $width) {
            $height++;
        }

        $cleaned_image = new Imagick();
        $cleaned_image->newImage($width, $height + 1, new ImagickPixel('black'));
        $cleaned_image->setImageFormat($image->getImageFormat());

        $iterator = $cleaned_image->getPixelIterator();
        $count = 0;

        foreach ($iterator as $row => $pixels) {
            foreach ($pixels as $col => $pixel) {
                try {
                    $pixel->setColor('rgba('.$colors[$count]['r'].', '.$colors[$count]['g'].',  '.$colors[$count]['b'].', '.$colors[$count]['a'].')');
                } catch (Exception $exception) {
                }
                $count++;
            }
            $iterator->syncIterator();
        }

        $this->saveFile($cleaned_image, 'cleaned');

        // Создаём палитру
        $filter = Imagick::FILTER_BOX;
        $palette_image = clone $cleaned_image;
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

        $this->saveFile($palette_image, 'resize');
    }

    protected function saveFile(Imagick $image, string $postfix = null)
    {
        $filename = pathinfo($this->filename, PATHINFO_FILENAME);
        $extension = pathinfo($this->filename, PATHINFO_EXTENSION);

        $this->images[$postfix] = '/media/v1/'.Str::slug($filename).'/'.Str::slug($filename).'-'.$postfix.'.'.$extension;

        Storage::drive('public')->put('v1/'.Str::slug($filename).'/'.Str::slug($filename).'-'.$postfix.'.'.$extension, $image);
    }
}
