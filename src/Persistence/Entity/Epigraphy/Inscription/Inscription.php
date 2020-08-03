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

use App\Persistence\Entity\Epigraphy\Carrier\Carrier;
use App\Persistence\Entity\Epigraphy\StringifiableEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\Epigraphy\Inscription\InscriptionRepository")
 */
class Inscription implements StringifiableEntityInterface
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
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Epigraphy\Carrier\Carrier", cascade={"persist"})
     */
    private $carrier;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $conventionalDate;

    /**
     * @var ZeroRow|null
     *
     * @ORM\OneToOne(targetEntity="App\Persistence\Entity\Epigraphy\Inscription\ZeroRow", cascade={"persist", "remove"})
     */
    private $zeroRow;

    /**
     * @var Collection|Interpretation[]
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\Inscription\Interpretation",
     *     cascade={"persist"},
     *     mappedBy="inscription",
     *     orphanRemoval=true
     * )
     */
    private $interpretations;

    public function __construct()
    {
        $this->interpretations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getId();
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

    public function getConventionalDate(): ?string
    {
        return $this->conventionalDate;
    }

    public function setConventionalDate(?string $conventionalDate): self
    {
        $this->conventionalDate = $conventionalDate;

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
