<?php

declare(strict_types=1);

/*
 * This file is part of «Epigraphy of Medieval Rus» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus» database is distributed
 * in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\Persistence\Entity\Media;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class File
{
    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_name", type="string", length=255, unique=true)
     */
    private $fileName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="media_type", type="string", length=255)
     */
    private $mediaType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url", type="string", length=2048, nullable=true)
     */
    private $url;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="binary_content", type="binary", length=16777215, nullable=true)
     */
    private $binaryContent;

    /**
     * @var string|null
     *
     * @ORM\Column(name="hash", type="string", length=32, options={"fixed" = true}, unique=true)
     */
    private $hash;

    /**
     * @var array|null
     *
     * @ORM\Column(name="metadata", type="json", nullable=true)
     */
    private $metadata;

    public function __toString(): string
    {
        return (string) $this->fileName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function setMediaType(?string $mediaType): self
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBinaryContent(): ?string
    {
        return $this->binaryContent;
    }

    public function setBinaryContent(?string $binaryContent): self
    {
        $this->binaryContent = $binaryContent;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function setOsfFileId(string $osfFileId): self
    {
        $metadata = $this->getMetadata() ?? [];

        if (!\array_key_exists('osf', $metadata)) {
            $metadata['osf'] = [];
        }

        $metadata['osf']['id'] = $osfFileId;

        $this->setMetadata($metadata);

        return $this;
    }
}
