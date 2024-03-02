<?php

namespace App\Services\YaColors\Models;

use App\Services\YaColors\ImageProcessing\ImageFileHandler;
use App\Services\YaColors\ImageProcessing\PaletteCreator;
use App\Services\YaColors\Models\FileInfo;
use App\Services\YaColors\Models\Paths;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;

/**
 * @property FileInfo $fileInfo
 * @property Paths $paths
 * @property array $palette
 */
class Image
{
    protected FileInfo $fileInfo;

    protected Paths $paths;

    protected array $palette = [];

    public function __construct($data = null)
    {
        $this->fileInfo = new FileInfo($data['fileInfo'] ?? null);
        $this->paths = new Paths($data['paths'] ?? null);
        $this->palette = $data['palette'] ?? [];
    }

    public static function all()
    {
        return ImageFileHandler::all();
    }

    public static function find(string $id)
    {
        return ImageFileHandler::loadData($id);
    }

    public static function findByName(string $name)
    {
        return ImageFileHandler::findByName($name);
    }

    public static function create(UploadedFile $file): self
    {
        $model = self::findByName($file->getClientOriginalName());

        if ($model) {
            return $model;
        }

        $model = new self();
        $model->fileInfo->id = Str::uuid()->toString();

        $pathinfo = pathinfo($file->getClientOriginalName());

        $model->paths->base = Str::slug($pathinfo['filename']);
        $model->paths->extension = $pathinfo['extension'];

        $stored_filename = $file->storeAs(
            $model->paths->filename,
            $model->fileInfo->id . '.' . $model->paths->extension,
            ['disk' => 'tmp_media']
        );
        $stored_filepath = Storage::disk('tmp_media')->path($stored_filename);

        $original_image = new Imagick($stored_filepath);
        $model->paths->originalImage = ImageFileHandler::saveImage($model, $original_image, 'original');

        $cleaned_image = PaletteCreator::cleanImage($original_image);
        $model->paths->cleanedImage = ImageFileHandler::saveImage($model, $cleaned_image, 'cleaned');

        PaletteCreator::createPalette($model, $cleaned_image);

        ImageFileHandler::saveData($model);

        return $model;
    }

    public function update()
    {
        $cleaned_image = new Imagick(public_path($this->paths->cleanedImage));
        PaletteCreator::createPalette($this, $cleaned_image);

        foreach ($this->paths->paletteImage as $version => $image_path) {
            if (!ImageFileHandler::hasImage($this, $version)) {
                ImageFileHandler::deleteImage($this, $version);
                unset($this->palette[$version]);
                $this->paths->deletePalette($version);
            }
        }

        ImageFileHandler::saveData($this);
    }

    public function delete()
    {
        ImageFileHandler::deleteData($this);
    }

    public function setFileInfo(FileInfo $fileInfo): self
    {
        $this->fileInfo = $fileInfo;

        return $this;
    }

    public function setPaths(Paths $paths): self
    {
        $this->paths = $paths;

        return $this;
    }

    public function getPalette(string $version): array
    {
        if (key_exists($version, $this->palette)) {
            return $this->palette[$version];
        }

        return [];
    }

    public function setPalette(string $version, array $palette): self
    {
        $this->palette[$version] = $palette;

        return $this;
    }

    public function addPalette(string $version, string $palette): self
    {
        $this->palette[$version][] = $palette;

        return $this;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        throw new Exception('Свойство «' . $name . '» не найдено');
    }

    public function __serialize(): array
    {
        return [
            'fileInfo' => $this->fileInfo,
            'paths' => $this->paths,
            'palette' => $this->palette,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->fileInfo = $data['fileInfo'] ?? null;
        $this->paths = $data['paths'] ?? null;
        $this->palette = $data['palette'] ?? [];
    }
}
