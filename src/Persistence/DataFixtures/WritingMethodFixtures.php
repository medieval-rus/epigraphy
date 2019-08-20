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

namespace App\Persistence\DataFixtures;

use App\Persistence\Entity\WritingMethod;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class WritingMethodFixtures extends Fixture
{
    public const reference_graffito = 'граффито';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadObject($manager, 'граффито', self::reference_graffito);

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param string        $name
     * @param string        $reference
     */
    private function loadObject(ObjectManager $manager, string $name, string $reference): void
    {
        $writingMethod = (new WritingMethod())
            ->setName($name)
        ;

        $this->addReference($reference, $writingMethod);

        $manager->persist($writingMethod);
    }
}
