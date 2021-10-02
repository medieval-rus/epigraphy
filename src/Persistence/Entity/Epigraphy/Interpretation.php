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

use App\Persistence\Entity\Media\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\Epigraphy\InterpretationRepository")
 */
class Interpretation implements StringifiableEntityInterface
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
     * @var Inscription
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Inscription",
     *     cascade={"persist"},
     *     inversedBy="interpretations"
     * )
     * @ORM\JoinColumn(nullable=false)
     */
    private $inscription;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=255)
     */
    private $source;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", length=65535, nullable=true)
     */
    private $comment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="page_numbers_in_source", type="string", length=255, nullable=true)
     */
    private $pageNumbersInSource;

    /**
     * @var string|null
     *
     * @ORM\Column(name="number_in_source", type="string", length=255, nullable=true)
     */
    private $numberInSource;

    /**
     * @var string|null
     *
     * @ORM\Column(name="origin", type="text", length=65535, nullable=true)
     */
    private $origin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="place_on_carrier", type="text", length=65535, nullable=true)
     */
    private $placeOnCarrier;

    /**
     * @var Collection|WritingType[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\WritingType", cascade={"persist"})
     * @ORM\JoinTable(name="interpretation_writing_type")
     */
    private $writingTypes;

    /**
     * @var Collection|WritingMethod[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\WritingMethod", cascade={"persist"})
     * @ORM\JoinTable(name="interpretation_writing_method")
     */
    private $writingMethods;

    /**
     * @var Collection|PreservationState[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\PreservationState", cascade={"persist"})
     * @ORM\JoinTable(name="interpretation_preservation_state")
     */
    private $preservationStates;

    /**
     * @var Collection|Material[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Material", cascade={"persist"})
     * @ORM\JoinTable(name="interpretation_material")
     */
    private $materials;

    /**
     * @var Collection|Alphabet[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Alphabet", cascade={"persist"})
     * @ORM\JoinTable(name="interpretation_alphabet")
     */
    private $alphabets;

    /**
     * @var string|null
     *
     * @ORM\Column(name="text", type="text", length=65535, nullable=true)
     */
    private $text;

    /**
     * @var Collection|File[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Media\File", cascade={"persist"})
     * @ORM\JoinTable(name="interpretation_text_images")
     */
    private $textImages;

    /**
     * @var string|null
     *
     * @ORM\Column(name="transliteration", type="text", length=65535, nullable=true)
     */
    private $transliteration;

    /**
     * @var string|null
     *
     * @ORM\Column(name="translation", type="text", length=65535, nullable=true)
     */
    private $translation;

    /**
     * @var Collection|ContentCategory[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\ContentCategory", cascade={"persist"})
     * @ORM\JoinTable(name="interpretation_content_category")
     */
    private $contentCategories;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=true)
     */
    private $content;

    /**
     * @var string|null
     *
     * @ORM\Column(name="date_in_text", type="text", length=65535, nullable=true)
     */
    private $dateInText;

    /**
     * @var string|null
     *
     * @ORM\Column(name="stratigraphical_date", type="text", length=65535, nullable=true)
     */
    private $stratigraphicalDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="non_stratigraphical_date", type="text", length=65535, nullable=true)
     */
    private $nonStratigraphicalDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="historical_date", type="text", length=65535, nullable=true)
     */
    private $historicalDate;

    public function __construct()
    {
        $this->writingTypes = new ArrayCollection();
        $this->writingMethods = new ArrayCollection();
        $this->preservationStates = new ArrayCollection();
        $this->materials = new ArrayCollection();
        $this->alphabets = new ArrayCollection();
        $this->textImages = new ArrayCollection();
        $this->contentCategories = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s', (string) $this->getSource());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getInscription(): ?Inscription
    {
        return $this->inscription;
    }

    public function setInscription(?Inscription $inscription): self
    {
        $this->inscription = $inscription;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
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

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): self
    {
        $this->origin = $origin;

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

    /**
     * @return Collection|WritingType[]
     */
    public function getWritingTypes(): Collection
    {
        return $this->writingTypes;
    }

    /**
     * @param Collection|WritingType[] $writingTypes
     */
    public function setWritingTypes(Collection $writingTypes): self
    {
        $this->writingTypes = $writingTypes;

        return $this;
    }

    /**
     * @return Collection|WritingMethod[]
     */
    public function getWritingMethods(): Collection
    {
        return $this->writingMethods;
    }

    /**
     * @param Collection|WritingMethod[] $writingMethods
     */
    public function setWritingMethods(Collection $writingMethods): self
    {
        $this->writingMethods = $writingMethods;

        return $this;
    }

    /**
     * @return Collection|PreservationState[]
     */
    public function getPreservationStates(): Collection
    {
        return $this->preservationStates;
    }

    /**
     * @param Collection|PreservationState[] $preservationStates
     */
    public function setPreservationStates(Collection $preservationStates): self
    {
        $this->preservationStates = $preservationStates;

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

    public function getAlphabets(): Collection
    {
        return $this->alphabets;
    }

    public function setAlphabets(Collection $alphabets): self
    {
        $this->alphabets = $alphabets;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getTextImages(): Collection
    {
        return $this->textImages;
    }

    /**
     * @param Collection|File[] $textImages
     */
    public function setTextImages(Collection $textImages): self
    {
        $this->textImages = $textImages;

        return $this;
    }

    public function getTransliteration(): ?string
    {
        return $this->transliteration;
    }

    public function setTransliteration(?string $transliteration): self
    {
        $this->transliteration = $transliteration;

        return $this;
    }

    public function getTranslation(): ?string
    {
        return $this->translation;
    }

    public function setTranslation(?string $translation): self
    {
        $this->translation = $translation;

        return $this;
    }

    /**
     * @return Collection|ContentCategory[]
     */
    public function getContentCategories(): Collection
    {
        return $this->contentCategories;
    }

    /**
     * @return Collection|ContentCategory[]
     */
    public function setContentCategories(Collection $contentCategories): self
    {
        $this->contentCategories = $contentCategories;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDateInText(): ?string
    {
        return $this->dateInText;
    }

    public function setDateInText(?string $dateInText): self
    {
        $this->dateInText = $dateInText;

        return $this;
    }

    public function getStratigraphicalDate(): ?string
    {
        return $this->stratigraphicalDate;
    }

    public function setStratigraphicalDate(?string $stratigraphicalDate): self
    {
        $this->stratigraphicalDate = $stratigraphicalDate;

        return $this;
    }

    public function getNonStratigraphicalDate(): ?string
    {
        return $this->nonStratigraphicalDate;
    }

    public function setNonStratigraphicalDate(?string $nonStratigraphicalDate): self
    {
        $this->nonStratigraphicalDate = $nonStratigraphicalDate;

        return $this;
    }

    public function getHistoricalDate(): ?string
    {
        return $this->historicalDate;
    }

    public function setHistoricalDate(?string $historicalDate): self
    {
        $this->historicalDate = $historicalDate;

        return $this;
    }
}
