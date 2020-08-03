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

namespace App\Persistence\DataFixtures\Epigraphy\Carrier;

use App\Persistence\DataFixtures\Epigraphy\Carrier\Category\CarrierCategoryFixtures;
use App\Persistence\DataFixtures\Epigraphy\Carrier\Type\CarrierTypeFixtures;
use App\Persistence\Entity\Epigraphy\Carrier\Carrier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CarrierFixtures extends Fixture implements DependentFixtureInterface
{
    public const CARRIER_1 = 'carrier 1';

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            CarrierTypeFixtures::class,
            CarrierCategoryFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->createCarrier1());

        $manager->flush();
    }

    private function createCarrier1(): Carrier
    {
        $carrier = (new Carrier())
            ->setType($this->getReference(CarrierTypeFixtures::PREDMETY_SVETSKOGO_NAZNACHENIYA))
            ->setCategory($this->getReference(CarrierCategoryFixtures::PRYASLITSE))
            ->setOrigin1('Любеч')
            ->setOrigin2('раскопки феодального замка под руководством Б.А. Рыбакова, перекоп у западного угла замка')
            ->setFindCircumstances(null)
            ->setCharacteristics(null)
            ->setIndividualName(null)
            ->setStoragePlace(null)
            ->setInventoryNumber(null)
        ;

        $this->addReference(self::CARRIER_1, $carrier);

        return $carrier;
    }
}
