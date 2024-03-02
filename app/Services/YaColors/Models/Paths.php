<?php

namespace App\Services\YaColors\Models;

use App\Services\YaColors\Traits\GetterSetter;

/**
 * @property string $base
 * @property string $extension
 * @property string $originalImage
 * @property string $cleanedImage
 * @property array $paletteImage
 */
class Paths
{
    use GetterSetter;

    protected string|null $base;
    protected string|null $extension;
    protected string|null $originalImage;
    protected string|null $cleanedImage;
    protected array $paletteImage;

    public static function create(array $data = null): self
    {
        return new self($data);
    }

    public function __construct(array $data = null)
    {
        $this->base = $data['base'] ?? null;
        $this->extension = $data['extension'] ?? null;
        $this->originalImage = $data['originalImage'] ?? null;
        $this->cleanedImage = $data['cleanedImage'] ?? null;
        $this->paletteImage = $data['paletteImage'] ?? [];
    }

    public function addPalette(string $version, string $path)
    {
        if (!key_exists($version, $this->paletteImage)) {
            $this->paletteImage[$version] = $path;
        }
    }

    public function deletePalette(string $version)
    {
        if (key_exists($version, $this->paletteImage)) {
            unset($this->paletteImage[$version]);
        }
    }

    public function getPaletteImage(string $version): string|null
    {
        if (key_exists($version, $this->paletteImage)) {
            return $this->paletteImage[$version];
        }

        return null;
    }

    public function __serialize(): array
    {
        return [
            'base' => $this->base,
            'extension' => $this->extension,
            'originalImage' => $this->originalImage,
            'cleanedImage' => $this->cleanedImage,
            'paletteImage' => $this->paletteImage,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->base = $data['base'] ?? null;
        $this->extension = $data['extension'] ?? null;
        $this->originalImage = $data['originalImage'] ?? null;
        $this->cleanedImage = $data['cleanedImage'] ?? null;
        $this->paletteImage = $data['paletteImage'] ?? [];
    }
}
