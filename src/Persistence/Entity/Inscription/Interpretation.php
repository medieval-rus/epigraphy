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
     * @ORM\Column(type="string", nullable=true)
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $photoFileName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $sketchFileName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $date;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $commentFileName;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Inscription
     */
    public function getInscription(): Inscription
    {
        return $this->inscription;
    }

    /**
     * @param Inscription $inscription
     *
     * @return Interpretation
     */
    public function setInscription(Inscription $inscription): self
    {
        $this->inscription = $inscription;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     *
     * @return Interpretation
     */
    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getDoWeAgree(): ?bool
    {
        return $this->doWeAgree;
    }

    /**
     * @param bool|null $doWeAgree
     *
     * @return Interpretation
     */
    public function setDoWeAgree(?bool $doWeAgree): self
    {
        $this->doWeAgree = $doWeAgree;

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
     * @return Interpretation
     */
    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTextImageFileName(): ?string
    {
        return $this->textImageFileName;
    }

    /**
     * @param string|null $textImageFileName
     *
     * @return Interpretation
     */
    public function setTextImageFileName(?string $textImageFileName): self
    {
        $this->textImageFileName = $textImageFileName;

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
     * @return Interpretation
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
     * @return Interpretation
     */
    public function setTranslation(?string $translation): self
    {
        $this->translation = $translation;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhotoFileName(): ?string
    {
        return $this->photoFileName;
    }

    /**
     * @param string|null $photoFileName
     *
     * @return Interpretation
     */
    public function setPhotoFileName(?string $photoFileName): self
    {
        $this->photoFileName = $photoFileName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSketchFileName(): ?string
    {
        return $this->sketchFileName;
    }

    /**
     * @param string|null $sketchFileName
     *
     * @return Interpretation
     */
    public function setSketchFileName(?string $sketchFileName): self
    {
        $this->sketchFileName = $sketchFileName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDate(): ?string
    {
        return $this->date;
    }

    /**
     * @param string|null $date
     *
     * @return Interpretation
     */
    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommentFileName(): ?string
    {
        return $this->commentFileName;
    }

    /**
     * @param string|null $commentFileName
     *
     * @return Interpretation
     */
    public function setCommentFileName(?string $commentFileName): self
    {
        $this->commentFileName = $commentFileName;

        return $this;
    }
}
