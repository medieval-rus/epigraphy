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

namespace App\Persistence\Entity\Epigraphy;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\Epigraphy\ZeroRowRepository")
 */
class ZeroRow
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
     * @var Inscription
     *
     * @ORM\OneToOne(targetEntity="App\Persistence\Entity\Epigraphy\Inscription", mappedBy="zeroRow")
     */
    private $inscription;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $placeOnCarrier;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_place_on_carrier_references")
     */
    private $placeOnCarrierReferences;

    /**
     * @var Collection|WritingType[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\WritingType", cascade={"persist"})
     */
    private $writingTypes;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_writing_type_references")
     */
    private $writingTypesReferences;

    /**
     * @var Collection|WritingMethod[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\WritingMethod", cascade={"persist"})
     */
    private $writingMethods;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_writing_method_references")
     */
    private $writingMethodsReferences;

    /**
     * @var Collection|PreservationState[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\PreservationState", cascade={"persist"})
     */
    private $preservationStates;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_preservation_state_references")
     */
    private $preservationStatesReferences;

    /**
     * @var Collection|Material[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Material", cascade={"persist"})
     */
    private $materials;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_material_references")
     */
    private $materialsReferences;

    /**
     * @var Collection|Alphabet[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Alphabet", cascade={"persist"})
     */
    private $alphabets;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_alphabet_references")
     */
    private $alphabetsReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_text_references")
     */
    private $textReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $textImageFileNames;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_text_image_file_names_references")
     */
    private $textImageFileNamesReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $transliteration;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_transliteration_references")
     */
    private $transliterationReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $translation;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_translation_references")
     */
    private $translationReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $photoFileNames;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_photo_file_names_references")
     */
    private $photoFileNamesReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $sketchFileNames;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_sketch_file_names_references")
     */
    private $sketchFileNamesReferences;

    /**
     * @var Collection|ContentCategory[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\ContentCategory", cascade={"persist"})
     */
    private $contentCategories;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_content_category_references")
     */
    private $contentCategoriesReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_content_references")
     */
    private $contentReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $dateInText;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_date_in_text_references")
     */
    private $dateInTextReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $stratigraphicalDate;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_stratigraphical_date_references")
     */
    private $stratigraphicalDateReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $nonStratigraphicalDate;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_non_stratigraphical_date_references")
     */
    private $nonStratigraphicalDateReferences;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $historicalDate;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Interpretation")
     * @ORM\JoinTable(name="zero_row_historical_date_references")
     */
    private $historicalDateReferences;

    public function __construct()
    {
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
        $this->textImageFileNamesReferences = new ArrayCollection();
        $this->transliterationReferences = new ArrayCollection();
        $this->translationReferences = new ArrayCollection();
        $this->photoFileNamesReferences = new ArrayCollection();
        $this->sketchFileNamesReferences = new ArrayCollection();
        $this->contentCategories = new ArrayCollection();
        $this->contentCategoriesReferences = new ArrayCollection();
        $this->contentReferences = new ArrayCollection();
        $this->dateInTextReferences = new ArrayCollection();
        $this->stratigraphicalDateReferences = new ArrayCollection();
        $this->nonStratigraphicalDateReferences = new ArrayCollection();
        $this->historicalDateReferences = new ArrayCollection();
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

    public function getTextImageFileNames(): ?string
    {
        return $this->textImageFileNames;
    }

    public function setTextImageFileNames(?string $textImageFileNames): self
    {
        $this->textImageFileNames = $textImageFileNames;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getTextImageFileNamesReferences(): Collection
    {
        return $this->textImageFileNamesReferences;
    }

    /**
     * @param Collection|Interpretation[] $textImageFileNamesReferences
     */
    public function setTextImageFileNamesReferences(Collection $textImageFileNamesReferences): self
    {
        $this->textImageFileNamesReferences = $textImageFileNamesReferences;

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

    public function getPhotoFileNames(): ?string
    {
        return $this->photoFileNames;
    }

    public function setPhotoFileNames(?string $photoFileNames): self
    {
        $this->photoFileNames = $photoFileNames;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getPhotoFileNamesReferences(): Collection
    {
        return $this->photoFileNamesReferences;
    }

    /**
     * @param Collection|Interpretation[] $photoFileNamesReferences
     */
    public function setPhotoFileNamesReferences(Collection $photoFileNamesReferences): self
    {
        $this->photoFileNamesReferences = $photoFileNamesReferences;

        return $this;
    }

    public function getSketchFileNames(): ?string
    {
        return $this->sketchFileNames;
    }

    public function setSketchFileNames(?string $sketchFileNames): self
    {
        $this->sketchFileNames = $sketchFileNames;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getSketchFileNamesReferences(): Collection
    {
        return $this->sketchFileNamesReferences;
    }

    /**
     * @param Collection|Interpretation[] $sketchFileNamesReferences
     */
    public function setSketchFileNamesReferences(Collection $sketchFileNamesReferences): self
    {
        $this->sketchFileNamesReferences = $sketchFileNamesReferences;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getContentReferences(): Collection
    {
        return $this->contentReferences;
    }

    /**
     * @param Collection|Interpretation[] $contentReferences
     */
    public function setContentReferences(Collection $contentReferences): self
    {
        $this->contentReferences = $contentReferences;

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

    public function getStratigraphicalDate(): ?string
    {
        return $this->stratigraphicalDate;
    }

    public function setStratigraphicalDate(?string $stratigraphicalDate): self
    {
        $this->stratigraphicalDate = $stratigraphicalDate;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getStratigraphicalDateReferences(): Collection
    {
        return $this->stratigraphicalDateReferences;
    }

    /**
     * @param Collection|Interpretation[] $stratigraphicalDateReferences
     */
    public function setStratigraphicalDateReferences(Collection $stratigraphicalDateReferences): self
    {
        $this->stratigraphicalDateReferences = $stratigraphicalDateReferences;

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
     * @return Collection|Interpretation[]
     */

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