<?php

namespace App\Services\YaColors;

use App\Services\YaColors\Handlers\V1;
use App\Services\YaColors\Handlers\V2;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;
use ImagickPixel;

class ImageModel
{
    protected string $basename;

    protected string $extension;

    protected string $filename;

    protected string $id;

    protected string $version;

    protected array $images;

    protected array $palette;

    public static function create(UploadedFile $file, string $version = 'v1'): self
    {
        $model = new self();
        $pathinfo = pathinfo($file->getClientOriginalName());
        $model->extension = $pathinfo['extension'];
        $model->filename = Str::slug($pathinfo['filename']);
        $model->basename = $model->filename . '.' . $model->extension;
        $model->id = Str::uuid();
        $model->version = $version;

        $stored_filename = $file->storeAs(
            $model->filename,
            $model->id . '.' . $model->extension,
            ['disk' => 'tmp_media']
        );
        $stored_filepath = Storage::disk('tmp_media')->path($stored_filename);

        $original_image = new Imagick($stored_filepath);
        $model->saveImage($original_image, 'original');

        $cleaned_image = $model->cleanImage($original_image);
        $model->saveImage($cleaned_image, 'cleaned');

        $palette_image = $model->createPalette($cleaned_image);
        $model->saveImage($palette_image, 'palette');

        $model->saveData();

        return $model;
    }

    public static function load(string $id)
    {
        if (Storage::disk('public_media')->exists($id)) {
            $data = Storage::disk('public_media')->json($id . '/data.json');

            $model = new self();
            $model->extension = $data['extension'];
            $model->filename = $data['filename'];
            $model->basename = $data['basename'];
            $model->id = $data['id'];
            $model->version = $data['version'];
            $model->images = $data['images'];
            $model->palette = $data['palette'];

            return $model;
        }

        abort(404, 'Объект не найден');
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        throw new Exception('Свойство «' . $name . '» не найдено');
    }

    protected function cleanImage(Imagick $image): Imagick
    {
        $newPixels = [];

        $iterator = $image->getPixelIterator();
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
        $cleaned_image->setImageFormat($image->getImageFormat());
        $cleaned_image->importImagePixels(0, 0, $newWidth, $newHeight, 'RGB', Imagick::PIXEL_CHAR, $newPixels);

        return $cleaned_image;
    }

    public function createPalette(Imagick $image)
    {
        $handler_class = match ($this->version) {
            'v1' => V1::class,
            'v2' => V2::class,
        };

        $handler = new $handler_class();

        if ($handler instanceof HandlerInterface) {
            $result = $handler->createPalette($image);
            $this->palette = $result['palette'];

            return $result['image'];
        }
    }

    protected function saveImage(Imagick $image, string $postfix = null)
    {
        $this->images[$postfix] = $this->getFilepath($postfix, '/media');

        Storage::drive('public_media')->put($this->getFilepath($postfix), $image);
    }

    protected function saveData()
    {
        $data = [
            'basename' => $this->basename,
            'extension' => $this->extension,
            'filename' => $this->filename,
            'id' => $this->id,
            'version' => $this->version,
            'images' => $this->images,
            'palette' => $this->palette,
        ];

        Storage::disk('public_media')->put($this->id . '/data.json', json_encode($data));
    }

    protected function getFilepath(string $postfix, string $prefix = null)
    {
        if ($prefix) {
            return $prefix . '/' . $this->id . '/' . $this->filename . '-' . $postfix . '.' . $this->extension;
        }

        return $this->id . '/' . $this->filename . '-' . $postfix . '.' . $this->extension;
    }
}
