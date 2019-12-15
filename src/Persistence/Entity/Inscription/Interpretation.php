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

use App\Persistence\Entity\ContentCategory;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\Inscription\InterpretationRepository")
 */
class Interpretation
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
     * @ORM\ManyToOne(
     *     targetEntity="App\Persistence\Entity\Inscription\Inscription",
     *     cascade={"persist"},
     *     inversedBy="interpretations"
     * )
     * @ORM\JoinColumn(name="inscription_id", referencedColumnName="id", nullable=false)
     */
    private $inscription;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $source;

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
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $doWeAgree;

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
    private $textImageFileName;

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
    private $photoFileName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sketchFileName;

    /**
     * @var ContentCategory
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\ContentCategory", cascade={"persist"})
     */
    private $contentCategory;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $content;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dateInText;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stratigraphicalDate;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nonStratigraphicalDate;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $historicalDate;

    // todo rework as conventional dates cells
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $conventionalDate;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

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

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

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

    public function getDoWeAgree(): ?bool
    {
        return $this->doWeAgree;
    }

    public function setDoWeAgree(?bool $doWeAgree): self
    {
        $this->doWeAgree = $doWeAgree;

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

    public function getTextImageFileName(): ?string
    {
        return $this->textImageFileName;
    }

    public function setTextImageFileName(?string $textImageFileName): self
    {
        $this->textImageFileName = $textImageFileName;

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

    public function getPhotoFileName(): ?string
    {
        return $this->photoFileName;
    }

    public function setPhotoFileName(?string $photoFileName): self
    {
        $this->photoFileName = $photoFileName;

        return $this;
    }

    public function getSketchFileName(): ?string
    {
        return $this->sketchFileName;
    }

    public function setSketchFileName(?string $sketchFileName): self
    {
        $this->sketchFileName = $sketchFileName;

        return $this;
    }

    public function getContentCategory(): ContentCategory
    {
        return $this->contentCategory;
    }

    public function setContentCategory(ContentCategory $contentCategory): self
    {
        $this->contentCategory = $contentCategory;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
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

    public function getConventionalDate(): string
    {
        return $this->conventionalDate;
    }

    public function setConventionalDate(string $conventionalDate): self
    {
        $this->conventionalDate = $conventionalDate;

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
}
