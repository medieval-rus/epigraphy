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

namespace App\Persistence\Entity\Bibliography;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="bibliography__author")
 * @ORM\Entity()
 */
class Author
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=255, unique=true)
     */
    private $fullName;

    /**
     * @var Collection|BibliographicRecord[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Bibliography\BibliographicRecord",
     *     cascade={"persist"},
     *     mappedBy="authors"
     * )
     */
    private $bibliographicRecords;

    public function __construct()
    {
        $this->bibliographicRecords = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->fullName;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @return Collection|BibliographicRecord[]
     */
    public function getBibliographicRecords(): Collection
    {
        return $this->bibliographicRecords;
    }

    /**
     * @param Collection|BibliographicRecord[] $bibliographicRecords
     */
    public function setBibliographicRecords(Collection $bibliographicRecords): self
    {
        $this->bibliographicRecords = $bibliographicRecords;

        return $this;
    }
}
