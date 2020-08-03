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

use App\Persistence\Entity\Epigraphy\Alphabet;
use App\Persistence\Entity\Epigraphy\ContentCategory;
use App\Persistence\Entity\Epigraphy\PreservationState;
use App\Persistence\Entity\Epigraphy\WritingMethod;
use App\Persistence\Entity\Epigraphy\WritingType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @ORM\MappedSuperclass()
 */
class InscriptionData
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
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $placeOnCarrier;

    /**
     * @var WritingType|null
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Epigraphy\WritingType", cascade={"persist"})
     */
    private $writingType;

    /**
     * @var WritingMethod|null
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Epigraphy\WritingMethod", cascade={"persist"})
     */
    private $writingMethod;

    /**
     * @var PreservationState|null
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Epigraphy\PreservationState", cascade={"persist"})
     */
    private $preservationState;

    /**
     * @var Alphabet|null
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Epigraphy\Alphabet", cascade={"persist"})
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $textImageFileNames;

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
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoFileNames;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sketchFileNames;

    /**
     * @var ContentCategory|null
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Epigraphy\ContentCategory", cascade={"persist"})
     */
    private $contentCategory;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $dateInText;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $stratigraphicalDate;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $nonStratigraphicalDate;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $historicalDate;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

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

    public function getPhotoFileNames(): ?string
    {
        return $this->photoFileNames;
    }

    public function setPhotoFileNames(?string $photoFileNames): self
    {
        $this->photoFileNames = $photoFileNames;

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

    public function getContentCategory(): ?ContentCategory
    {
        return $this->contentCategory;
    }

    public function setContentCategory(?ContentCategory $contentCategory): self
    {
        $this->contentCategory = $contentCategory;

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
