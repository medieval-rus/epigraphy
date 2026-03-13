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
class CarrierCategory implements NamedEntityInterface
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
     * @ORM\Column(name="is_super_category", type="boolean", nullable=true)
     */
    private $isSuperCategory;

    /**
     * @ORM\OneToMany(targetEntity="CarrierCategory", mappedBy="supercategory")
     */
    private $subcategories;

    /**
     * @ORM\ManyToOne(targetEntity="CarrierCategory", inversedBy="subcategories")
     * @ORM\JoinColumn(name="supercategory_id", referencedColumnName="id")
     */
    private $supercategory;

    /**
     * @var Collection|CarrierCategoryTranslation[]
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Persistence\Entity\Epigraphy\CarrierCategoryTranslation",
     *     mappedBy="carrierCategory",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $translations;

    public function __construct() {
        $this->subcategories = new ArrayCollection();
        $this->translations = new ArrayCollection();
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

    public function getTranslatedName(?string $locale): ?string
    {
        $normalizedLocale = null === $locale ? null : strtolower($locale);

        if (null !== $normalizedLocale) {
            foreach ($this->translations as $translation) {
                if ($translation->getLocale() === $normalizedLocale) {
                    return $translation->getName();
                }
            }

            if (false !== strpos($normalizedLocale, '_')) {
                $normalizedLocale = substr($normalizedLocale, 0, (int) strpos($normalizedLocale, '_'));
            }

            if (false !== strpos($normalizedLocale, '-')) {
                $normalizedLocale = substr($normalizedLocale, 0, (int) strpos($normalizedLocale, '-'));
            }

            foreach ($this->translations as $translation) {
                if ($translation->getLocale() === $normalizedLocale) {
                    return $translation->getName();
                }
            }
        }

        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSupercategory(): ?self
    {
        return $this->supercategory;
    }

    public function getSubcategories(): PersistentCollection
    {
        return $this->subcategories;
    }

    public function setSupercategory(self $superid): self
    {
        $this->supercategory = $superid;

        return $this;
    }

    public function setIsSuperCategory(?bool $isSuperCategory): self
    {
        $this->isSuperCategory = $isSuperCategory;

        return $this;
    }

    public function getIsSuperCategory(): ?bool
    {
        return $this->isSuperCategory;
    }

    /**
     * @return Collection|CarrierCategoryTranslation[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(CarrierCategoryTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setCarrierCategory($this);
        }

        return $this;
    }

    public function removeTranslation(CarrierCategoryTranslation $translation): self
    {
        $this->translations->removeElement($translation);

        return $this;
    }
}
