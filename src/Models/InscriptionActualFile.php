<?php

namespace App\Models;

use App\Persistence\Entity\Media\File;

final class InscriptionActualFile
{
    private File $file;
    private ?string $description;

    public function __construct(File $file, ?string $description)
    {
        $this->file = $file;
        $this->description = $description;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}