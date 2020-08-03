<?php

declare(strict_types=1);

/*
 * This file is part of «Epigraphy of Medieval Rus'» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus'» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus'» database is distributed
 * in the hope  that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus'» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\Persistence\Entity\Epigraphy\Inscription;

use App\Persistence\Entity\Epigraphy\Material;
use App\Persistence\Entity\Epigraphy\StringifiableEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\Epigraphy\Inscription\InterpretationRepository")
 */
class Interpretation extends InscriptionData implements StringifiableEntityInterface
{
    /**
     * @var Inscription
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Inscription",
     *     cascade={"persist"},
     *     inversedBy="interpretations"
     * )
     * @ORM\JoinColumn(nullable=false)
     */
    private $inscription;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $source;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pageNumbersInSource;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numberInSource;

    /**
     * @var Collection|Material[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Material", cascade={"persist"})
     */
    private $materials;

    public function __construct()
    {
        $this->materials = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s', (string) $this->getSource());
    }

    public function getInscription(): ?Inscription
    {
        return $this->inscription;
    }

    public function setInscription(Inscription $inscription): self
    {
        $this->inscription = $inscription;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getPageNumbersInSource(): ?string
    {
        return $this->pageNumbersInSource;
    }

    public function setPageNumbersInSource(?string $pageNumbersInSource): self
    {
        $this->pageNumbersInSource = $pageNumbersInSource;

        return $this;
    }

    public function getNumberInSource(): ?string
    {
        return $this->numberInSource;
    }

    public function setNumberInSource(?string $numberInSource): self
    {
        $this->numberInSource = $numberInSource;

        return $this;
    }

    /**
     * @return Collection|Material[]
     */
    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    /**
     * @param Collection|Material[] $materials
     */
    public function setMaterials(Collection $materials): self
    {
        $this->materials = $materials;

        return $this;
    }
}
