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

namespace App\Persistence\Entity\Epigraphy;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Inscription implements StringifiableEntityInterface
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
     * @ORM\Column(name="number", type="string", length=255, nullable=true)
     */
    private $number;

    /**
     * @var Carrier|null
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Epigraphy\Carrier", cascade={"persist"})
     * @ORM\JoinTable(name="inscription_carrier")
     */
    private $carrier;

    /**
     * @var string|null
     *
     * @ORM\Column(name="conventional_date", type="string", length=255, nullable=true)
     */
    private $conventionalDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="date_explanation", type="text", length=65535, nullable=true)
     */
    private $dateExplanation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", length=65535, nullable=true)
     */
    private $comment;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_shown_on_site", type="boolean", options={"default": false})
     */
    private $isShownOnSite;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_part_of_corpus", type="boolean", options={"default": true})
     */
    private $isPartOfCorpus;

    /**
     * @var ZeroRow|null
     *
     * @ORM\OneToOne(
     *     targetEntity="App\Persistence\Entity\Epigraphy\ZeroRow",
     *     cascade={"persist", "remove"},
     *     inversedBy="inscription"
     * )
     */
    private $zeroRow;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"},
     *     mappedBy="inscription",
     *     orphanRemoval=true
     * )
     * @ORM\OrderBy({"source" = "ASC"})
     */
    private $interpretations;

    public function __construct()
    {
        $this->isShownOnSite = false;
        $this->isPartOfCorpus = false;
        $this->zeroRow = new ZeroRow();
        $this->interpretations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }

    public function __clone()
    {
        $this->id = null;
        $this->number = null;
        $this->isShownOnSite = false;

        $oldInterpretations = $this->interpretations;
        $this->interpretations = new ArrayCollection();

        $clonesById = [];
        foreach ($oldInterpretations as $oldInterpretation) {
            $clonedInterpretation = clone $oldInterpretation;
            $clonedInterpretation->setInscription($this);

            $this->interpretations->add($clonedInterpretation);

            $clonesById[$oldInterpretation->getId()] = $clonedInterpretation;
        }

        $oldZeroRow = $this->zeroRow;
        $this->zeroRow = clone $this->zeroRow;

        $f = fn (Interpretation $oldInterpretation): Interpretation => $clonesById[$oldInterpretation->getId()];

        $this->zeroRow->setOriginReferences($oldZeroRow->getOriginReferences()->map($f));
        $this->zeroRow->setPlaceOnCarrierReferences($oldZeroRow->getPlaceOnCarrierReferences()->map($f));
        $this->zeroRow->setWritingTypesReferences($oldZeroRow->getWritingTypesReferences()->map($f));
        $this->zeroRow->setWritingMethodsReferences($oldZeroRow->getWritingMethodsReferences()->map($f));
        $this->zeroRow->setPreservationStatesReferences($oldZeroRow->getPreservationStatesReferences()->map($f));
        $this->zeroRow->setMaterialsReferences($oldZeroRow->getMaterialsReferences()->map($f));
        $this->zeroRow->setAlphabetsReferences($oldZeroRow->getAlphabetsReferences()->map($f));
        $this->zeroRow->setTextReferences($oldZeroRow->getTextReferences()->map($f));
        $this->zeroRow->setTextImagesReferences($oldZeroRow->getTextImagesReferences()->map($f));
        $this->zeroRow->setTransliterationReferences($oldZeroRow->getTransliterationReferences()->map($f));
        $this->zeroRow->setTranslationReferences($oldZeroRow->getTranslationReferences()->map($f));
        $this->zeroRow->setContentCategoriesReferences($oldZeroRow->getContentCategoriesReferences()->map($f));
        $this->zeroRow->setDescriptionReferences($oldZeroRow->getDescriptionReferences()->map($f));
        $this->zeroRow->setDateInTextReferences($oldZeroRow->getDateInTextReferences()->map($f));
        $this->zeroRow->setStratigraphicalDateReferences($oldZeroRow->getStratigraphicalDateReferences()->map($f));
        $this->zeroRow->setNonStratigraphicalDateReferences($oldZeroRow->getNonStratigraphicalDateReferences()->map($f));
        $this->zeroRow->setHistoricalDateReferences($oldZeroRow->getHistoricalDateReferences()->map($f));
        $this->zeroRow->setPhotosReferences($oldZeroRow->getPhotosReferences()->map($f));
        $this->zeroRow->setDrawingsReferences($oldZeroRow->getDrawingsReferences()->map($f));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        if ($id === null) {
            return $this;
        }
        $this->id = $id;
        return $this;
    }
    
    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): void
    {
        $this->number = $number;
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

    public function getConventionalDate(): ?string
    {
        return $this->conventionalDate;
    }

    public function setConventionalDate(?string $conventionalDate): self
    {
        $this->conventionalDate = $conventionalDate;

        return $this;
    }

    public function getDateExplanation(): ?string
    {
        return $this->dateExplanation;
    }

    public function setDateExplanation(?string $dateExplanation): self
    {
        $this->dateExplanation = $dateExplanation;

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

    public function getIsShownOnSite(): ?bool
    {
        return $this->isShownOnSite;
    }

    public function setIsShownOnSite(bool $isShownOnSite): self
    {
        $this->isShownOnSite = $isShownOnSite;

        return $this;
    }

    public function getIsPartOfCorpus(): ?bool
    {
        return $this->isPartOfCorpus;
    }

    public function setIsPartOfCorpus(bool $isPartOfCorpus): self
    {
        $this->isPartOfCorpus = $isPartOfCorpus;

        return $this;
    }

    public function getZeroRow(): ?ZeroRow
    {
        return $this->zeroRow;
    }

    public function setZeroRow(?ZeroRow $zeroRow): self
    {
        $this->zeroRow = $zeroRow;

        if (null !== $zeroRow) {
            $this->zeroRow->setInscription($this);
        }

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getInterpretations(): Collection
    {
        return $this->interpretations;
    }

    public function addInterpretation(Interpretation $interpretation): self
    {
        if (!$this->interpretations->contains($interpretation)) {
            $this->interpretations[] = $interpretation;

            $interpretation->setInscription($this);
        }

        return $this;
    }

    public function removeInterpretation(Interpretation $interpretation): self
    {
        if ($this->interpretations->contains($interpretation)) {
            $this->interpretations->removeElement($interpretation);

            if ($interpretation->getInscription() === $this) {
                $interpretation->setInscription(null);
            }
        }

        return $this;
    }
}
