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

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="carrier_category_translation",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="carrier_category_locale_unique", columns={"carrier_category_id", "locale"})
 *     },
 *     indexes={
 *         @ORM\Index(name="carrier_category_translation_locale_idx", columns={"locale"}),
 *         @ORM\Index(name="carrier_category_translation_name_idx", columns={"name"})
 *     }
 * )
 */
class CarrierCategoryTranslation implements StringifiableEntityInterface
{
    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var CarrierCategory|null
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Persistence\Entity\Epigraphy\CarrierCategory",
     *     inversedBy="translations"
     * )
     * @ORM\JoinColumn(name="carrier_category_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $carrierCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=5)
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    public function __toString(): string
    {
        return sprintf('%s (%s)', (string) $this->name, (string) $this->locale);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCarrierCategory(): ?CarrierCategory
    {
        return $this->carrierCategory;
    }

    public function setCarrierCategory(?CarrierCategory $carrierCategory): self
    {
        $this->carrierCategory = $carrierCategory;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = strtolower($locale);

        return $this;
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
}
