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

namespace App\Persistence\Entity\Carrier;

use App\Persistence\Entity\Carrier\Category\CarrierCategory;
use App\Persistence\Entity\Carrier\Type\CarrierType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\Carrier\CarrierRepository")
 */
class Carrier
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
     * @var CarrierType
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Carrier\Type\CarrierType", cascade={"persist"})
     */
    private $type;

    /**
     * @var CarrierCategory
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Carrier\Category\CarrierCategory", cascade={"persist"})
     */
    private $category;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $origin1;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $origin2;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $individualName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $storagePlace;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $inventoryNumber;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): CarrierType
    {
        return $this->type;
    }

    public function setType(CarrierType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCategory(): CarrierCategory
    {
        return $this->category;
    }

    public function setCategory(CarrierCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getOrigin1(): ?string
    {
        return $this->origin1;
    }

    public function setOrigin1(?string $origin1): self
    {
        $this->origin1 = $origin1;

        return $this;
    }

    public function getOrigin2(): ?string
    {
        return $this->origin2;
    }

    public function setOrigin2(?string $origin2): self
    {
        $this->origin2 = $origin2;

        return $this;
    }

    public function getIndividualName(): ?string
    {
        return $this->individualName;
    }

    public function setIndividualName(?string $individualName): self
    {
        $this->individualName = $individualName;

        return $this;
    }

    public function getStoragePlace(): ?string
    {
        return $this->storagePlace;
    }

    public function setStoragePlace(?string $storagePlace): self
    {
        $this->storagePlace = $storagePlace;

        return $this;
    }

    public function getInventoryNumber(): ?string
    {
        return $this->inventoryNumber;
    }

    public function setInventoryNumber(?string $inventoryNumber): self
    {
        $this->inventoryNumber = $inventoryNumber;

        return $this;
    }
}
