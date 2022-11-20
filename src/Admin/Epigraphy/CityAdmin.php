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
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class CityAdmin extends AbstractNamedEntityAdmin
{
    protected $baseRouteName = 'epigraphy_city';

    protected $baseRoutePattern = 'epigraphy/city';

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name', null, $this->createFormOptions('name'))
            ->add(
                'nameAliases',
                CollectionType::class,
                $this->createFormOptions(
                    'nameAliases',
                    [
                        'entry_type' => TextType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'delete_empty' => true,
                        'required' => false
                    ]
                )
            )
            ->add(
                'type',
                ChoiceType::class,
                $this->createFormOptions(
                    'type',
                    [
                        'required' => false,
                        'choices' => [
                            'Город' => 'Город',
                            'Поселок' => 'Поселок',
                            'Село' => 'Село',
                            'Деревня' => 'Деревня',
                        ]
                    ]
                )
            )
            ->add(
                'country',
                ChoiceType::class,
                $this->createFormOptions(
                    'country',
                    [
                        'required' => false,
                        'choices' => [
                            'Россия' => 'Россия',
                            'Украина' => 'Украина',
                            'Белоруссия' => 'Белоруссия',
                            'Италия' => 'Италия',
                            'Франция' => 'Франция',
                            'Турция' => 'Турция',
                        ]
                    ]
                )
            )
            ->add('region', null, $this->createFormOptions('region'))
        ;
    }
}