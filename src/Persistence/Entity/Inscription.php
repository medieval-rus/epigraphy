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

namespace App\Persistence\Entity;

use App\Persistence\Entity\Carrier\Carrier;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\InscriptionRepository")
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
     * @ORM\ManyToOne(targetEntity="WritingMethod", cascade={"persist"})
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $newText;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $transliteration;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $translation;

    /**
     * @var ContentCategory|null
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\ContentCategory", cascade={"persist"})
     */
    private $contentCategory;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $dateInText;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $commentOnDate;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $commentOnText;

    public function __construct()
    {
        $this->materials = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Carrier|null
     */
    public function getCarrier(): ?Carrier
    {
        return $this->carrier;
    }

    /**
     * @param Carrier|null $carrier
     *
     * @return Inscription
     */
    public function setCarrier(?Carrier $carrier): self
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsInSitu(): ?bool
    {
        return $this->isInSitu;
    }

    /**
     * @param bool|null $isInSitu
     *
     * @return Inscription
     */
    public function setIsInSitu(?bool $isInSitu): self
    {
        $this->isInSitu = $isInSitu;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlaceOnCarrier(): ?string
    {
        return $this->placeOnCarrier;
    }

    /**
     * @param string|null $placeOnCarrier
     *
     * @return Inscription
     */
    public function setPlaceOnCarrier(?string $placeOnCarrier): self
    {
        $this->placeOnCarrier = $placeOnCarrier;

        return $this;
    }

    /**
     * @return WritingType|null
     */
    public function getWritingType(): ?WritingType
    {
        return $this->writingType;
    }

    /**
     * @param WritingType|null $writingType
     *
     * @return Inscription
     */
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

    /**
     * @param Material $material
     *
     * @return Inscription
     */
    public function addMaterial(Material $material): self
    {
        if (!$this->materials->contains($material)) {
            $this->materials[] = $material;
        }

        return $this;
    }

    /**
     * @param Material $material
     *
     * @return Inscription
     */
    public function removeMaterial(Material $material): self
    {
        if ($this->materials->contains($material)) {
            $this->materials->removeElement($material);
        }

        return $this;
    }

    /**
     * @return WritingMethod|null
     */
    public function getWritingMethod(): ?WritingMethod
    {
        return $this->writingMethod;
    }

    /**
     * @param WritingMethod|null $writingMethod
     *
     * @return Inscription
     */
    public function setWritingMethod(?WritingMethod $writingMethod): self
    {
        $this->writingMethod = $writingMethod;

        return $this;
    }

    /**
     * @return PreservationState|null
     */
    public function getPreservationState(): ?PreservationState
    {
        return $this->preservationState;
    }

    /**
     * @param PreservationState|null $preservationState
     *
     * @return Inscription
     */
    public function setPreservationState(?PreservationState $preservationState): self
    {
        $this->preservationState = $preservationState;

        return $this;
    }

    /**
     * @return Alphabet|null
     */
    public function getAlphabet(): ?Alphabet
    {
        return $this->alphabet;
    }

    /**
     * @param Alphabet|null $alphabet
     *
     * @return Inscription
     */
    public function setAlphabet(?Alphabet $alphabet): self
    {
        $this->alphabet = $alphabet;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     *
     * @return Inscription
     */
    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNewText(): ?string
    {
        return $this->newText;
    }

    /**
     * @param string|null $newText
     *
     * @return Inscription
     */
    public function setNewText(?string $newText): self
    {
        $this->newText = $newText;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTransliteration(): ?string
    {
        return $this->transliteration;
    }

    /**
     * @param string|null $transliteration
     *
     * @return Inscription
     */
    public function setTransliteration(?string $transliteration): self
    {
        $this->transliteration = $transliteration;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTranslation(): ?string
    {
        return $this->translation;
    }

    /**
     * @param string|null $translation
     *
     * @return Inscription
     */
    public function setTranslation(?string $translation): self
    {
        $this->translation = $translation;

        return $this;
    }

    /**
     * @return ContentCategory|null
     */
    public function getContentCategory(): ?ContentCategory
    {
        return $this->contentCategory;
    }

    /**
     * @param ContentCategory|null $contentCategory
     *
     * @return Inscription
     */
    public function setContentCategory(?ContentCategory $contentCategory): self
    {
        $this->contentCategory = $contentCategory;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDateInText(): ?string
    {
        return $this->dateInText;
    }

    /**
     * @param string|null $dateInText
     *
     * @return Inscription
     */
    public function setDateInText(?string $dateInText): self
    {
        $this->dateInText = $dateInText;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommentOnDate(): ?string
    {
        return $this->commentOnDate;
    }

    /**
     * @param string|null $commentOnDate
     *
     * @return Inscription
     */
    public function setCommentOnDate(?string $commentOnDate): self
    {
        $this->commentOnDate = $commentOnDate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommentOnText(): ?string
    {
        return $this->commentOnText;
    }

    /**
     * @param string|null $commentOnText
     *
     * @return Inscription
     */
    public function setCommentOnText(?string $commentOnText): self
    {
        $this->commentOnText = $commentOnText;

        return $this;
    }
}
