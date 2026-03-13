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

namespace App\Admin\Epigraphy;

use App\Admin\AbstractNamedEntityAdmin;
use App\Persistence\Entity\Epigraphy\LocalizedText;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class RiverTypeAdmin extends AbstractNamedEntityAdmin
{
    private const LOCALIZED_FIELDS = [
        'name' => 'string',
    ];

    protected $baseRouteName = 'epigraphy_river_type';

    protected $baseRoutePattern = 'epigraphy/river-type';

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name', null, $this->createFormOptions('name'))
            ->add(
                $this->getLocalizedTextFieldNameForTarget(LocalizedText::TARGET_RIVER_TYPE, 'name'),
                TextType::class,
                $this->createLocalizedTextOptionsForTarget(
                    LocalizedText::TARGET_RIVER_TYPE,
                    null === $this->getSubject() ? null : $this->getSubject()->getId(),
                    'name'
                )
            )
        ;
    }

    public function postPersist($object): void
    {
        $this->storeLocalizedTextFieldsForTarget(
            LocalizedText::TARGET_RIVER_TYPE,
            null === $object ? null : $object->getId(),
            self::LOCALIZED_FIELDS
        );
    }

    public function postUpdate($object): void
    {
        $this->storeLocalizedTextFieldsForTarget(
            LocalizedText::TARGET_RIVER_TYPE,
            null === $object ? null : $object->getId(),
            self::LOCALIZED_FIELDS
        );
    }
}
