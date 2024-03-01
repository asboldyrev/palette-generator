<?php

namespace App\Services\YaColors\ImageProcessing;

use App\Services\YaColors\Exceptions\ModelNotFoundException;
use App\Services\YaColors\Models\Image;
use Imagick;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageFileHandler
{
    public static function all()
    {
        $images = collect([]);

        foreach (Storage::disk('public_media')->allDirectories() as $image_id) {
            $images->push(self::loadData($image_id));
        }

        return $images;
    }

    public static function loadData(string $id): Image
    {
        if (Storage::disk('public_media')->exists($id)) {
            $data = Storage::disk('public_media')->get($id . '/data.json');
            $model = unserialize($data);

            return $model;
        }

        throw new ModelNotFoundException();
    }

    public static function findByName(string $name): Image|null
    {
        $pathinfo = pathinfo($name);
        $name = Str::slug($pathinfo['filename']);

        /** @var Image $image */
        foreach (self::all() as $image) {
            if ($image->paths->base == $name) {
                return $image;
            }
        }

        return null;
    }

    public static function saveImage(Image $image, Imagick $imagick, string $postfix = null)
    {
        Storage::drive('public_media')->put(self::getFilepath($image, $postfix), $imagick);

        return self::getFilepath($image, $postfix, '/media');
    }

    public static function saveData(Image $image)
    {
        Storage::disk('public_media')->put($image->fileInfo->id . '/data.json', serialize($image));
    }

    public static function getFilepath(Image $image, string $postfix, string $prefix = null)
    {
        if ($prefix) {
            return $prefix . '/' . $image->fileInfo->id . '/' . $image->paths->base . '-' . $postfix . '.' . $image->paths->extension;
        }

        return $image->fileInfo->id . '/' . $image->paths->base . '-' . $postfix . '.' . $image->paths->extension;
    }
}
