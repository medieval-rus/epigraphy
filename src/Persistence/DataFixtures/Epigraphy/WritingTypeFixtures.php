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

namespace App\Persistence\DataFixtures\Epigraphy;

use App\Persistence\Entity\Epigraphy\WritingType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class WritingTypeFixtures extends Fixture
{
    public const REFERENCE_VTOROY = self::class.'второй';

    public function load(ObjectManager $manager): void
    {
        $this->loadObject($manager, 'второй', self::REFERENCE_VTOROY);

        $manager->flush();
    }

    private function loadObject(ObjectManager $manager, string $name, string $reference): void
    {
        $writingType = (new WritingType())
            ->setName($name)
        ;

        $this->addReference($reference, $writingType);

        $manager->persist($writingType);
    }
}