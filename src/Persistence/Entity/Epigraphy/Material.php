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
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity()
 */
class Material implements NamedEntityInterface
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
     * @var bool|null
     *
     * @ORM\Column(name="is_super_material", type="boolean", nullable=true)
     */
    private $isSuperMaterial;
    
    /**
     * @ORM\OneToMany(targetEntity="Material", mappedBy="supermaterial")
     */
    private $submaterials;

    /**
     * @ORM\ManyToOne(targetEntity="Material", inversedBy="submaterials")
     * @ORM\JoinColumn(name="supermaterial_id", referencedColumnName="id")
     */
    private $supermaterial;

    public function __construct() {
        $this->submaterials = new ArrayCollection();
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

    public function getSupermaterial(): ?self
    {
        return $this->supermaterial;
    }

    public function setSupermaterial(?self $supermaterial): self
    {
        $this->supermaterial = $supermaterial;

        return $this;
    }

    public function getSubmaterials(): PersistentCollection
    {
        return $this->submaterials;
    }

    public function setIsSuperMaterial(?bool $isSuperMaterial): self
    {
        $this->isSuperMaterial = $isSuperMaterial;

        return $this;
    }
    
    public function getIsSuperMaterial(): ?bool
    {
        return $this->isSuperMaterial;
    }
}
