<?php

namespace App\Services\YaColors\Models;

use App\Services\YaColors\Traits\GetterSetter;

/**
 * @property string $id;
 * @property string $version;
 */
class FileInfo
{
    use GetterSetter;

    protected string|null $id;

    public static function create(array $data = null): self
    {
        return new self($data);
    }

    public function __construct(array $data = null)
    {
        $this->id = $data['id'] ?? null;
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'] ?? null;
    }
}
