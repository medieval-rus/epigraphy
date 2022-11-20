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

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity()
 */
class DiscoverySite implements NamedEntityInterface
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var array|string[]
     *
     * @ORM\Column(name="name_aliases", type="simple_array", nullable=true)
     */
    private $nameAliases;

    /**
     * @var Collection|River[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\River", cascade={"persist"})
     * @ORM\JoinTable(name="discovery_site_river")
     */
    private $rivers;

    /**
     * @var Collection|City[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\City", cascade={"persist"})
     * @ORM\JoinTable(name="discovery_site_city")
     */
    private $cities;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_outside_city", type="boolean", options={"default": false})
     */
    private $isOutsideCity;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_outside_river", type="boolean", options={"default": false})
     */
    private $isOutsideRiver;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comments", type="string", nullable=true)
     */
    private $comments;

    /**
     * @var int|null
     *
     * @ORM\Column(name="latitude", type="integer", nullable=true)
     */
    private $latitude;

    /**
     * @var int|null
     *
     * @ORM\Column(name="longitude", type="integer", nullable=true)
     */
    private $longitude;

    public function __construct()
    {
        $this->rivers = new ArrayCollection();
        $this->cities = new ArrayCollection();
        $this->nameAliases = array();
        $this->isOutsideRiver = false;
        $this->isOutsideCity = false;
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNameAliases(): array
    {
        return $this->nameAliases;
    }

    public function setNameAliases(?array $nameAlias): self
    {
        $this->nameAliases = $nameAlias ?: array();
        return $this;
    }

    public function getLatitude(): ?int
    {
        return $this->latitude;
    }

    public function setLatitude(int $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?int
    {
        return $this->longitude;
    }

    public function setLongitude(int $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return Collection|River[]
     */
    public function getRivers(): Collection
    {
        return $this->rivers;
    }

    public function setRivers(Collection $river): self
    {
        $this->rivers = $river;
        return $this;
    }

    /**
     * @return Collection|City[]
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function setCities(Collection $city): self
    {
        $this->cities = $city;
        return $this;
    }

    public function getIsOutsideRiver(): ?bool
    {
        return $this->isOutsideRiver;
    }

    public function setIsOutsideRiver(bool $isOutsideRiver): self
    {
        $this->isOutsideRiver = $isOutsideRiver;

        return $this;
    }

    public function getIsOutsideCity(): ?bool
    {
        return $this->isOutsideCity;
    }

    public function setIsOutsideCity(bool $isOutsideCity): self
    {
        $this->isOutsideCity = $isOutsideCity;

        return $this;
    }
}



