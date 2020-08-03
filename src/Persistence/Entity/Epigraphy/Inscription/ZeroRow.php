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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\Epigraphy\Inscription\ZeroRowRepository")
 */
class ZeroRow extends InscriptionData
{
    /**
     * @var Inscription
     *
     * @ORM\OneToOne(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Inscription", mappedBy="zeroRow")
     */
    private $inscription;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_place_on_carrier")
     */
    private $placeOnCarrierReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_writing_type")
     */
    private $writingTypeReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_writing_method")
     */
    private $writingMethodReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_preservation_state")
     */
    private $preservationStateReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_alphabet")
     */
    private $alphabetReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_text")
     */
    private $textReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_text_image_file_names")
     */
    private $textImageFileNamesReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_transliteration")
     */
    private $transliterationReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_translation")
     */
    private $translationReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_photo_file_names")
     */
    private $photoFileNamesReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_sketch_file_names")
     */
    private $sketchFileNamesReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_content_category")
     */
    private $contentCategoryReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_content")
     */
    private $contentReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_date_in_text")
     */
    private $dateInTextReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_stratigraphical_date")
     */
    private $stratigraphicalDateReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_non_stratigraphical_date")
     */
    private $nonStratigraphicalDateReferences;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_historical_date")
     */
    private $historicalDateReferences;

    /**
     * @var Collection|Material[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Material", cascade={"persist"})
     */
    private $materials;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation")
     * @ORM\JoinTable(name="zero_row_materials")
     */
    private $materialsReferences;

    public function __construct()
    {
        $this->placeOnCarrierReferences = new ArrayCollection();
        $this->writingTypeReferences = new ArrayCollection();
        $this->writingMethodReferences = new ArrayCollection();
        $this->preservationStateReferences = new ArrayCollection();
        $this->alphabetReferences = new ArrayCollection();
        $this->textReferences = new ArrayCollection();
        $this->textImageFileNamesReferences = new ArrayCollection();
        $this->transliterationReferences = new ArrayCollection();
        $this->translationReferences = new ArrayCollection();
        $this->photoFileNamesReferences = new ArrayCollection();
        $this->sketchFileNamesReferences = new ArrayCollection();
        $this->contentCategoryReferences = new ArrayCollection();
        $this->contentReferences = new ArrayCollection();
        $this->dateInTextReferences = new ArrayCollection();
        $this->stratigraphicalDateReferences = new ArrayCollection();
        $this->nonStratigraphicalDateReferences = new ArrayCollection();
        $this->historicalDateReferences = new ArrayCollection();
        $this->materials = new ArrayCollection();
        $this->materialsReferences = new ArrayCollection();
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
     * @return Collection|Interpretation[]
     */
    public function getWritingTypeReferences(): Collection
    {
        return $this->writingTypeReferences;
    }

    /**
     * @param Collection|Interpretation[] $writingTypeReferences
     */
    public function setWritingTypeReferences(Collection $writingTypeReferences): self
    {
        $this->writingTypeReferences = $writingTypeReferences;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getWritingMethodReferences(): Collection
    {
        return $this->writingMethodReferences;
    }

    /**
     * @param Collection|Interpretation[] $writingMethodReferences
     */
    public function setWritingMethodReferences(Collection $writingMethodReferences): self
    {
        $this->writingMethodReferences = $writingMethodReferences;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getPreservationStateReferences(): Collection
    {
        return $this->preservationStateReferences;
    }

    /**
     * @param Collection|Interpretation[] $preservationStateReferences
     */
    public function setPreservationStateReferences(Collection $preservationStateReferences): self
    {
        $this->preservationStateReferences = $preservationStateReferences;

        return $this;
    }

    /**
     * @return Collection|Interpretation[]
     */
    public function getAlphabetReferences(): Collection
    {
        return $this->alphabetReferences;
    }

    /**
     * @param Collection|Interpretation[] $alphabetReferences
     */
    public function setAlphabetReferences(Collection $alphabetReferences): self
    {
        $this->alphabetReferences = $alphabetReferences;

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
     * @return Collection|Interpretation[]
     */
    public function getContentCategoryReferences(): Collection
    {
        return $this->contentCategoryReferences;
    }

    /**
     * @param Collection|Interpretation[] $contentCategoryReferences
     */
    public function setContentCategoryReferences(Collection $contentCategoryReferences): self
    {
        $this->contentCategoryReferences = $contentCategoryReferences;

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
}
