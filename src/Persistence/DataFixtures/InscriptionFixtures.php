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
use App\Persistence\Entity\Inscription\Inscription;
use App\Persistence\Entity\Inscription\Interpretation;
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
            ->setCarrier($this->getReference(WallCarrierFixtures::REFERENCE_YUGO_VOSTOCHNYI_STOLB))
            ->setIsInSitu(true)
            ->setPlaceOnCarrier('южная грань столба')
            ->setWritingType($this->getReference(WritingTypeFixtures::REFERENCE_VTOROY))
            ->addMaterial($this->getReference(MaterialFixtures::REFERENCE_SHTUKATURKA))
            ->setWritingMethod($this->getReference(WritingMethodFixtures::REFERENCE_GRAFFITO))
            ->setPreservationState($this->getReference(PreservationStateFixtures::REFERENCE_TSELAYA))
            ->setAlphabet($this->getReference(AlphabetFixtures::REFERENCE_KIRILLITSA))
            ->setContentCategory($this->getReference(ContentCategoryFixtures::REFERENCE_PRESTAVISYA))
            ->setDateInText('22 мая на святого мученика Василиска')
            ->addInterpretation(
                (new Interpretation())
                    ->setSource('какой-то источник')
                    ->setDoWeAgree(true)
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
                    ->setTextImageFileName('изображениеТекста.джипег')
                    ->setTransliteration('это транслитерация')
                    ->setTranslation(<<<EOT
'М(есØца мая 22 (на) с(вØ)т(о)го м(у)ч(е)н(и)ка Василиска престависØ раб Божий архиеп(иско)п Климент'
EOT
                    )
                    ->setPhotoFileName('фото.джипег')
                    ->setSketchFileName('прорись.джипег')
                    ->setDate('10 век')
                    ->setCommentFileName('комментарий.пдф')
            )
        ;
    }
}
