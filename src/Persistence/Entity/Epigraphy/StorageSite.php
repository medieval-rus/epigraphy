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
class StorageSite implements NamedEntityInterface
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
     * @var Collection|City[]
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\City", cascade={"persist"})
     * @ORM\JoinTable(name="storage_site_city")
     */
    private $cities;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comments", type="string", nullable=true)
     */
    private $comments;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
        $this->nameAliases = array();
    }

    public function __toString(): string
    {
        return sprintf(
            '%s; %s; (id: %s)',
            (string) $this->getName(),
            implode(', ', $this->getCities()->toArray()),
            (string) $this->getId()
        );
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
}
