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

namespace App\Persistence\Entity\Inscription;

use App\Persistence\Entity\Alphabet;
use App\Persistence\Entity\Carrier\Carrier;
use App\Persistence\Entity\Material;
use App\Persistence\Entity\PreservationState;
use App\Persistence\Entity\WritingMethod;
use App\Persistence\Entity\WritingType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\Inscription\InscriptionRepository")
 */
class Inscription
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Carrier|null
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Carrier\Carrier", cascade={"persist"})
     */
    private $carrier;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isInSitu;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $placeOnCarrier;

    /**
     * @var WritingType|null
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\WritingType", cascade={"persist"})
     */
    private $writingType;

    /**
     * @var Collection|Material[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Material")
     */
    private $materials;

    /**
     * @var WritingMethod|null
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\WritingMethod", cascade={"persist"})
     */
    private $writingMethod;

    /**
     * @var PreservationState|null
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\PreservationState", cascade={"persist"})
     */
    private $preservationState;

    /**
     * @var Alphabet|null
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Alphabet", cascade={"persist"})
     */
    private $alphabet;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $majorPublications;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Persistence\Entity\Inscription\Interpretation",
     *     cascade={"persist"},
     *     mappedBy="inscription",
     *     orphanRemoval=true
     * )
     */
    private $interpretations;

    public function __construct()
    {
        $this->materials = new ArrayCollection();
        $this->interpretations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCarrier(): ?Carrier
    {
        return $this->carrier;
    }

    public function setCarrier(?Carrier $carrier): self
    {
        $this->carrier = $carrier;

        return $this;
    }

    public function getIsInSitu(): ?bool
    {
        return $this->isInSitu;
    }

    public function setIsInSitu(?bool $isInSitu): self
    {
        $this->isInSitu = $isInSitu;

        return $this;
    }

    public function getPlaceOnCarrier(): ?string
    {
        return $this->placeOnCarrier;
    }

    public function setPlaceOnCarrier(?string $placeOnCarrier): self
    {
        $this->placeOnCarrier = $placeOnCarrier;

        return $this;
    }

    public function getWritingType(): ?WritingType
    {
        return $this->writingType;
    }

    public function setWritingType(?WritingType $writingType): self
    {
        $this->writingType = $writingType;

        return $this;
    }

    /**
     * @return Collection|Material[]
     */
    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    public function addMaterial(Material $material): self
    {
        if (!$this->materials->contains($material)) {
            $this->materials[] = $material;
        }

        return $this;
    }

    public function removeMaterial(Material $material): self
    {
        if ($this->materials->contains($material)) {
            $this->materials->removeElement($material);
        }

        return $this;
    }

    public function getWritingMethod(): ?WritingMethod
    {
        return $this->writingMethod;
    }

    public function setWritingMethod(?WritingMethod $writingMethod): self
    {
        $this->writingMethod = $writingMethod;

        return $this;
    }

    public function getPreservationState(): ?PreservationState
    {
        return $this->preservationState;
    }

    public function setPreservationState(?PreservationState $preservationState): self
    {
        $this->preservationState = $preservationState;

        return $this;
    }

    public function getAlphabet(): ?Alphabet
    {
        return $this->alphabet;
    }

    public function setAlphabet(?Alphabet $alphabet): self
    {
        $this->alphabet = $alphabet;

        return $this;
    }

    public function getMajorPublications(): ?string
    {
        return $this->majorPublications;
    }

    public function setMajorPublications(?string $majorPublications): self
    {
        $this->majorPublications = $majorPublications;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getInterpretations(): Collection
    {
        return $this->interpretations;
    }

    /**
     * @param Collection|Interpretation[] $interpretations
     */
    public function setInterpretations(Collection $interpretations): self
    {
        $this->interpretations = new ArrayCollection();

        foreach ($interpretations as $interpretation) {
            $this->addInterpretation($interpretation);
        }

        return $this;
    }

    public function addInterpretation(Interpretation $interpretation): self
    {
        $interpretation->setInscription($this);

        $this->interpretations[] = $interpretation;

        return $this;
    }
}
