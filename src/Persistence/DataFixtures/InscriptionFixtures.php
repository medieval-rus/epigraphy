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

use App\Persistence\DataFixtures\Carrier\WallCarrierFixtures;
use App\Persistence\Entity\Inscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class InscriptionFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            WallCarrierFixtures::class,
            WritingTypeFixtures::class,
            MaterialFixtures::class,
            WritingMethodFixtures::class,
            PreservationStateFixtures::class,
            AlphabetFixtures::class,
            ContentCategoryFixtures::class,
        ];
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->createInscription1());

        $manager->flush();
    }

    /**
     * @return Inscription
     */
    private function createInscription1(): Inscription
    {
        return (new Inscription())
            ->setCarrier($this->getReference(WallCarrierFixtures::reference_yugoVostochnyiStolb))
            ->setIsInSitu(true)
            ->setPlaceOnCarrier('южная грань столба')
            ->setWritingType($this->getReference(WritingTypeFixtures::reference_vtoroy))
            ->addMaterial($this->getReference(MaterialFixtures::reference_shtukaturka))
            ->setWritingMethod($this->getReference(WritingMethodFixtures::reference_graffito))
            ->setPreservationState($this->getReference(PreservationStateFixtures::reference_tselaya))
            ->setAlphabet($this->getReference(AlphabetFixtures::reference_kirillitsa))
            ->setText(<<<EOT
мцама8к
~
в
ст~гомч
~
нкавасı
лискапрес...ви
сраби
~
иархиѥ
п
~
пъклиментъ
EOT
            )
            ->setNewText(null)
            ->setTransliteration(null)
            ->setTranslation('М(есØца мая 22 (на) с(вØ)т(о)го м(у)ч(е)н(и)ка Василиска престависØ раб Божий архиеп(иско)п Климент')
            ->setContentCategory($this->getReference(ContentCategoryFixtures::reference_prestavisya))
            ->setDateInText('22 мая на святого мученика Василиска')
            ->setCommentOnDate(null)
            ->setCommentOnText(null)
        ;
    }
}
