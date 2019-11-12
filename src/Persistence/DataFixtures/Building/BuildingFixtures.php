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

namespace App\Persistence\DataFixtures\Building;

use App\Persistence\DataFixtures\Building\Type\BuildingTypeFixtures;
use App\Persistence\Entity\Building\Building;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class BuildingFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_TSERKOV_NIKOLY_NA_LIPNE = self::class.'церковь Николы на Липне';

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            BuildingTypeFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadObject(
            $manager,
            'церковь Николы на Липне',
            BuildingTypeFixtures::REFERENCE_TSERKOV,
            self::REFERENCE_TSERKOV_NIKOLY_NA_LIPNE
        );

        $manager->flush();
    }

    private function loadObject(
        ObjectManager $manager,
        string $name,
        string $buildingTypeReference,
        string $reference
    ): void {
        $building = (new Building())
            ->setName($name)
            ->setType($this->getReference($buildingTypeReference))
        ;

        $this->addReference($reference, $building);

        $manager->persist($building);
    }
}
