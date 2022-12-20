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
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity()
 */
class River implements NamedEntityInterface
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var array
     * 
     * @ORM\Column(name="name_aliases", type="simple_array", nullable=true)
     */
    private $nameAliases;

    /**
     * @var null|RiverType
     *
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Epigraphy\RiverType", cascade={"persist"})
     * @ORM\JoinTable(name="river__river_type")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="River", mappedBy="superriver")
     */
    private $subrivers;

    /**
     * @ORM\ManyToOne(targetEntity="River", inversedBy="subrivers")
     * @ORM\JoinColumn(name="superriver_id", referencedColumnName="id")
     */
    private $superriver;

    public function __construct() {
        $this->nameAliases = array();
        $this->type = new ArrayCollection();
        $this->subrivers = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
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

    /**
     * @return Collection
     */
    public function getType(): Collection
    {
        return $this->type;
    }

    public function setType(Collection $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getSuperriver(): ?self
    {
        return $this->superriver;
    }

    public function setSuperriver(?self $superriver): self
    {
        $this->superriver = $superriver;

        return $this;
    }

    public function getSubrivers(): PersistentCollection
    {
        return $this->subrivers;
    }
}

