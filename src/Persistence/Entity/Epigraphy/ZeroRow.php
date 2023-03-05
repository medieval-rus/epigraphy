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
 * @ORM\Entity()
 */
class ZeroRow
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
     * @ORM\OneToOne(targetEntity="App\Persistence\Entity\Epigraphy\Inscription", mappedBy="zeroRow")
     */
    private $inscription;

    /**
     * @var string|null
     *
     * @ORM\Column(name="origin", type="text", length=65535, nullable=true)
     */
    private $origin;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_origin_references")
     */
    private $originReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(name="place_on_carrier", type="text", length=65535, nullable=true)
     */
    private $placeOnCarrier;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_place_on_carrier_references")
     */
    private $placeOnCarrierReferences;

    /**
     * @var Collection|WritingType[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\WritingType", cascade={"persist"})
     * @ORM\JoinTable(name="zero_row_writing_type")
     */
    private $writingTypes;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_writing_type_references")
     */
    private $writingTypesReferences;

    /**
     * @var Collection|WritingMethod[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\WritingMethod", cascade={"persist"})
     * @ORM\JoinTable(name="zero_row_writing_method")
     */
    private $writingMethods;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_writing_method_references")
     */
    private $writingMethodsReferences;

    /**
     * @var Collection|PreservationState[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\PreservationState", cascade={"persist"})
     * @ORM\JoinTable(name="zero_row_preservation_state")
     */
    private $preservationStates;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_preservation_state_references")
     */
    private $preservationStatesReferences;

    /**
     * @var Collection|Material[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Material", cascade={"persist"})
     * @ORM\JoinTable(name="zero_row_material")
     */
    private $materials;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_material_references")
     */
    private $materialsReferences;

    /**
     * @var Collection|Alphabet[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Alphabet", cascade={"persist"})
     * @ORM\JoinTable(name="zero_row_alphabet")
     */
    private $alphabets;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_alphabet_references")
     */
    private $alphabetsReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(name="interpretation_comment", type="text", length=65535, nullable=true)
     */
    private $interpretationComment; 

    /**
     * @var string|null
     *
     * @ORM\Column(name="text", type="text", length=65535, nullable=true)
     */
    private $text;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_text_references")
     */
    private $textReferences;

    /**
     * @var Collection|File[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Media\File", cascade={"persist"})
     * @ORM\JoinTable(name="zero_row_text_images")
     */
    private $textImages;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_text_images_references")
     */
    private $textImagesReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(name="transliteration", type="text", length=65535, nullable=true)
     */
    private $transliteration;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_transliteration_references")
     */
    private $transliterationReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reconstruction", type="text", length=65535, nullable=true)
     */
    private $reconstruction;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_reconstruction_references")
     */
    private $reconstructionReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(name="normalization", type="text", length=65535, nullable=true)
     */
    private $normalization;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_normalization_references")
     */
    private $normalizationReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(name="translation", type="text", length=65535, nullable=true)
     */
    private $translation;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_translation_references")
     */
    private $translationReferences;

    /**
     * @var Collection|ContentCategory[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\ContentCategory", cascade={"persist"})
     * @ORM\JoinTable(name="zero_row_content_category")
     */
    private $contentCategories;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_content_category_references")
     */
    private $contentCategoriesReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_description_references")
     */
    private $descriptionReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(name="date_in_text", type="text", length=65535, nullable=true)
     */
    private $dateInText;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_date_in_text_references")
     */
    private $dateInTextReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(name="non_stratigraphical_date", type="text", length=65535, nullable=true)
     */
    private $nonStratigraphicalDate;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_non_stratigraphical_date_references")
     */
    private $nonStratigraphicalDateReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(name="historical_date", type="text", length=65535, nullable=true)
     */
    private $historicalDate;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_historical_date_references")
     */
    private $historicalDateReferences;

    /**
     * @var Collection|File[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Media\File", cascade={"persist"})
     * @ORM\JoinTable(name="zero_row_photos")
     */
    private $photos;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_photos_references")
     */
    private $photosReferences;

    /**
     * @var Collection|File[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Media\File", cascade={"persist"})
     * @ORM\JoinTable(name="zero_row_drawings")
     */
    private $drawings;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Interpretation",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="zero_row_drawings_references")
     */
    private $drawingsReferences;

    public function __construct()
    {
        $this->originReferences = new ArrayCollection();
        $this->placeOnCarrierReferences = new ArrayCollection();
        $this->writingTypes = new ArrayCollection();
        $this->writingTypesReferences = new ArrayCollection();
        $this->writingMethods = new ArrayCollection();
        $this->writingMethodsReferences = new ArrayCollection();
        $this->preservationStates = new ArrayCollection();
        $this->preservationStatesReferences = new ArrayCollection();
        $this->materials = new ArrayCollection();
        $this->materialsReferences = new ArrayCollection();
        $this->alphabets = new ArrayCollection();
        $this->alphabetsReferences = new ArrayCollection();
        $this->textReferences = new ArrayCollection();
        $this->textImages = new ArrayCollection();
        $this->textImagesReferences = new ArrayCollection();
        $this->transliterationReferences = new ArrayCollection();
        $this->reconstructionReferences = new ArrayCollection();
        $this->normalizationReferences = new ArrayCollection();
        $this->translationReferences = new ArrayCollection();
        $this->contentCategories = new ArrayCollection();
        $this->contentCategoriesReferences = new ArrayCollection();
        $this->descriptionReferences = new ArrayCollection();
        $this->dateInTextReferences = new ArrayCollection();
        $this->stratigraphicalDateReferences = new ArrayCollection();
        $this->nonStratigraphicalDateReferences = new ArrayCollection();
        $this->historicalDateReferences = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->photosReferences = new ArrayCollection();
        $this->drawings = new ArrayCollection();
        $this->drawingsReferences = new ArrayCollection();
    }

    public function __clone()
    {
        $this->id = null;
        $this->originReferences = new ArrayCollection();
        $this->placeOnCarrierReferences = new ArrayCollection();
        $this->writingTypesReferences = new ArrayCollection();
        $this->writingMethodsReferences = new ArrayCollection();
        $this->preservationStatesReferences = new ArrayCollection();
        $this->materialsReferences = new ArrayCollection();
        $this->alphabetsReferences = new ArrayCollection();
        $this->textReferences = new ArrayCollection();
        $this->textImagesReferences = new ArrayCollection();
        $this->transliterationReferences = new ArrayCollection();
        $this->translationReferences = new ArrayCollection();
        $this->contentCategoriesReferences = new ArrayCollection();
        $this->descriptionReferences = new ArrayCollection();
        $this->dateInTextReferences = new ArrayCollection();
        $this->stratigraphicalDateReferences = new ArrayCollection();
        $this->nonStratigraphicalDateReferences = new ArrayCollection();
        $this->historicalDateReferences = new ArrayCollection();
        $this->photosReferences = new ArrayCollection();
        $this->drawingsReferences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInscription(): Inscription
    {
        return $this->inscription;
    }

    public function setInscription(Inscription $inscription): self
    {
        $this->inscription = $inscription;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): void
    {
        $this->origin = $origin;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getOriginReferences(): Collection
    {
        return $this->originReferences;
    }

    /**
     * @param Collection|Interpretation[] $originReferences
     */
    public function setOriginReferences(Collection $originReferences): self
    {
        $this->originReferences = $originReferences;

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
     * @return Collection|Interpretation[]
     */
    public function getPlaceOnCarrierReferences(): Collection
    {
        return $this->placeOnCarrierReferences;
    }

    /**
     * @param Collection|Interpretation[] $placeOnCarrierReferences
     */
    public function setPlaceOnCarrierReferences(Collection $placeOnCarrierReferences): self
    {
        $this->placeOnCarrierReferences = $placeOnCarrierReferences;

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
     * @return Collection|Interpretation[]
     */
    public function getWritingTypesReferences(): Collection
    {
        return $this->writingTypesReferences;
    }

    /**
     * @param Collection|Interpretation[] $writingTypesReferences
     */
    public function setWritingTypesReferences(Collection $writingTypesReferences): self
    {
        $this->writingTypesReferences = $writingTypesReferences;

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
     * @return Collection|Interpretation[]
     */
    public function getWritingMethodsReferences(): Collection
    {
        return $this->writingMethodsReferences;
    }

    /**
     * @param Collection|Interpretation[] $writingMethodsReferences
     */
    public function setWritingMethodsReferences(Collection $writingMethodsReferences): self
    {
        $this->writingMethodsReferences = $writingMethodsReferences;

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
     * @return Collection|Interpretation[]
     */
    public function getPreservationStatesReferences(): Collection
    {
        return $this->preservationStatesReferences;
    }

    /**
     * @param Collection|Interpretation[] $preservationStatesReferences
     */
    public function setPreservationStatesReferences(Collection $preservationStatesReferences): self
    {
        $this->preservationStatesReferences = $preservationStatesReferences;

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

    /**
     * @return Collection|Interpretation[]
     */
    public function getMaterialsReferences(): Collection
    {
        return $this->materialsReferences;
    }

    /**
     * @param Collection|Interpretation[] $materialReferences
     */
    public function setMaterialsReferences(Collection $materialReferences): self
    {
        $this->materialsReferences = $materialReferences;

        return $this;
    }

    /**
     * @return Collection|Alphabet[]
     */
    public function getAlphabets(): Collection
    {
        return $this->alphabets;
    }

    /**
     * @param Collection|Alphabet[] $alphabets
     */
    public function setAlphabets(Collection $alphabets): self
    {
        $this->alphabets = $alphabets;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getAlphabetsReferences(): Collection
    {
        return $this->alphabetsReferences;
    }

    /**
     * @param Collection|Interpretation[] $alphabetsReferences
     */
    public function setAlphabetsReferences(Collection $alphabetsReferences): self
    {
        $this->alphabetsReferences = $alphabetsReferences;

        return $this;
    }

    public function getInterpretationComment(): ?string
    {
        return $this->interpretationComment;
    }

    public function setInterpretationComment(?string $interpretationComment): self
    {
        $this->interpretationComment = $interpretationComment;

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
     * @return Collection|Interpretation[]
     */
    public function getTextReferences(): Collection
    {
        return $this->textReferences;
    }

    /**
     * @param Collection|Interpretation[] $textReferences
     */
    public function setTextReferences(Collection $textReferences): self
    {
        $this->textReferences = $textReferences;

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

    /**
     * @return Collection|Interpretation[]
     */
    public function getTextImagesReferences(): Collection
    {
        return $this->textImagesReferences;
    }

    /**
     * @param Collection|Interpretation[] $textImagesReferences
     */
    public function setTextImagesReferences(Collection $textImagesReferences): self
    {
        $this->textImagesReferences = $textImagesReferences;

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

    /**
     * @return Collection|Interpretation[]
     */
    public function getTransliterationReferences(): Collection
    {
        return $this->transliterationReferences;
    }

    /**
     * @param Collection|Interpretation[] $transliterationReferences
     */
    public function setTransliterationReferences(Collection $transliterationReferences): self
    {
        $this->transliterationReferences = $transliterationReferences;

        return $this;
    }

    public function getReconstruction(): ?string
    {
        return $this->reconstruction;
    }

    public function setReconstruction(?string $reconstruction): self
    {
        $this->reconstruction = $reconstruction;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getReconstructionReferences(): Collection
    {
        return $this->reconstructionReferences;
    }

    /**
     * @param Collection|Interpretation[]
     */
    public function setReconstructionReferences(Collection $reconstructionReferences): self
    {
        $this->reconstructionReferencenormalizations = $reconstructionReferences;

        return $this;
    }

    public function getNormalization(): ?string
    {
        return $this->normalization;
    }

    public function setNormalization(?string $normalization): self
    {
        $this->normalization = $normalization;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getNormalizationReferences(): Collection
    {
        return $this->normalizationReferences;
    }

    /**
     * @param Collection|Interpretation[]
     */
    public function setNormalizationReferences(Collection $normalizationReferences): self
    {
        $this->normalizationReferences = $normalizationReferences;

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
     * @return Collection|Interpretation[]
     */
    public function getTranslationReferences(): Collection
    {
        return $this->translationReferences;
    }

    /**
     * @param Collection|Interpretation[] $translationReferences
     */
    public function setTranslationReferences(Collection $translationReferences): self
    {
        $this->translationReferences = $translationReferences;

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

    /**
     * @return Collection|Interpretation[]
     */
    public function getContentCategoriesReferences(): Collection
    {
        return $this->contentCategoriesReferences;
    }

    /**
     * @param Collection|Interpretation[] $contentCategoriesReferences
     */
    public function setContentCategoriesReferences(Collection $contentCategoriesReferences): self
    {
        $this->contentCategoriesReferences = $contentCategoriesReferences;

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

    /**
     * @return Collection|Interpretation[]
     */
    public function getDescriptionReferences(): Collection
    {
        return $this->descriptionReferences;
    }

    /**
     * @param Collection|Interpretation[] $descriptionReferences
     */
    public function setDescriptionReferences(Collection $descriptionReferences): self
    {
        $this->descriptionReferences = $descriptionReferences;

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

    /**
     * @return Collection|Interpretation[]
     */
    public function getDateInTextReferences(): Collection
    {
        return $this->dateInTextReferences;
    }

    /**
     * @param Collection|Interpretation[] $dateInTextReferences
     */
    public function setDateInTextReferences(Collection $dateInTextReferences): self
    {
        $this->dateInTextReferences = $dateInTextReferences;

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

    /**
     * @return Collection|Interpretation[]
     */
    public function getNonStratigraphicalDateReferences(): Collection
    {
        return $this->nonStratigraphicalDateReferences;
    }

    /**
     * @param Collection|Interpretation[] $nonStratigraphicalDateReferences
     */
    public function setNonStratigraphicalDateReferences(Collection $nonStratigraphicalDateReferences): self
    {
        $this->nonStratigraphicalDateReferences = $nonStratigraphicalDateReferences;

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

    /**
     * @return Collection|Interpretation[]
     */
    public function getHistoricalDateReferences(): Collection
    {
        return $this->historicalDateReferences;
    }

    /**
     * @param Collection|Interpretation[] $historicalDateReferences
     */
    public function setHistoricalDateReferences(Collection $historicalDateReferences): self
    {
        $this->historicalDateReferences = $historicalDateReferences;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    /**
     * @param Collection|File[] $photos
     */
    public function setPhotos(Collection $photos): self
    {
        $this->photos = $photos;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getPhotosReferences(): Collection
    {
        return $this->photosReferences;
    }

    /**
     * @param Collection|Interpretation[] $photosReferences
     */
    public function setPhotosReferences(Collection $photosReferences): self
    {
        $this->photosReferences = $photosReferences;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getDrawings(): Collection
    {
        return $this->drawings;
    }

    /**
     * @param Collection|File[] $drawings
     */
    public function setDrawings(Collection $drawings): self
    {
        $this->drawings = $drawings;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getDrawingsReferences(): Collection
    {
        return $this->drawingsReferences;
    }

    /**
     * @param Collection|Interpretation[] $drawingsReferences
     */
    public function setDrawingsReferences(Collection $drawingsReferences): self
    {
        $this->drawingsReferences = $drawingsReferences;

        return $this;
    }
}
