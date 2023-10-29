<?php

namespace App\Services\YaColors\Handlers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;
use ImagickPixel;

abstract class AbstractHandler
{
    protected string $filename;

    protected $palette;

    protected array $images;

    public function __construct(UploadedFile $file, protected string $versionPrefix)
    {
        $stored_file = $file->move(storage_path('ya-colors/'.$this->versionPrefix), $file->getClientOriginalName());
        $this->filename = $stored_file->getFilename();

        $original_image = new Imagick($stored_file->getPathname());
        $this->saveImage($original_image, 'original');
        $cleaned_image = $this->cleanImage($original_image);
        $this->createPalette($cleaned_image);

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

    abstract protected function createPalette(Imagick $image): Imagick;

    protected function cleanImage(Imagick $image): Imagick
    {
        $newPixels = [];

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
                    $newPixels[] = $r;
                    $newPixels[] = $g;
                    $newPixels[] = $b;
                    // $newPixels[] = $a;
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
        $cleaned_image->setImageFormat($image->getImageFormat());
        $cleaned_image->importImagePixels(0, 0, $newWidth, $newHeight, 'RGB', Imagick::PIXEL_CHAR, $newPixels);

        $this->saveImage($cleaned_image, 'cleaned');

        return $cleaned_image;
    }

    protected function saveImage(Imagick $image, string $postfix = null): void
    {
        $filename = pathinfo($this->filename, PATHINFO_FILENAME);
        $extension = pathinfo($this->filename, PATHINFO_EXTENSION);

        $this->images[$postfix] = '/media/'.$this->versionPrefix.'/'.Str::slug($filename).'/'.Str::slug($filename).'-'.$postfix.'.'.$extension;

        Storage::drive('public')->put($this->versionPrefix.'/'.Str::slug($filename).'/'.Str::slug($filename).'-'.$postfix.'.'.$extension, $image);
    }

    protected function saveFile(mixed $data, string $filename): void
    {
        $filename = pathinfo($this->filename, PATHINFO_FILENAME);

        Storage::drive('public')->put($this->versionPrefix.'/'.Str::slug($filename).'/'.$filename, $data);
    }
}
