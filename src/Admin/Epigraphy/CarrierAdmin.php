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

use App\Admin\AbstractEntityAdmin;
use App\Persistence\Entity\Epigraphy\Carrier;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;

final class CarrierAdmin extends AbstractEntityAdmin
{
    protected $baseRouteName = 'epigraphy_carrier';

    protected $baseRoutePattern = 'epigraphy/carrier';

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createListOptions('id'))
            ->add('origin1', null, $this->createListOptions('origin1'))
            ->add('origin2', null, $this->createListOptions('origin2'))
            ->add('individualName', null, $this->createListOptions('individualName'))
            ->add('supercarrier', null, $this->createListOptions('supercarrier'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('individualName', null, $this->createFormOptions('individualName'))
            ->add('categories', null, $this->createManyToManyFormOptions('categories'))
            ->add(
                'supercarrier',
                EntityType::class,
                $this->createFilteredEntityOptions('supercarrier', Carrier::class, 'isSuperCarrier') 
            )
            ->add(
                'isSuperCarrier',
                CheckboxType::class,
                $this->createFormOptions('isSuperCarrier', ['required' => false])
            )
            ->add('characteristics', null, $this->createFormOptions('characteristics'))
            ->add('materials', null, $this->createManyToManyFormOptions('materials'))
            ->add('materialDescription', null, $this->createFormOptions('materialDescription'))
            ->add('stratigraphicalDate', null, $this->createFormOptions('stratigraphicalDate'))
            ->add('findCircumstances', null, $this->createFormOptions('findCircumstances'))
            ->add('carrierHistory', null, $this->createFormOptions('carrierHistory', ['required' => false]))
            ->add('discoverySite', null, $this->createManyToManyFormOptions('discoverySite'))
            // археология
            ->add('archaeology', null, $this->createFormOptions('archaeology', ['required' => false]))
            // предыдущие места хранения
            ->add('previousStorage', null, $this->createFormOptions('previousStorage', ['required' => false]))
            ->add('storageSite', null, $this->createManyToManyFormOptions('storageSite'))
            // локализация в месте хранения
            ->add('storageLocalization', null, $this->createFormOptions('storageLocalization', ['required' => false]))
            ->add('inventoryNumber', null, $this->createFormOptions('inventoryNumber'))
            // убрали in situ
            // ->add(
            //     'isInSitu',
            //     CheckboxType::class,
            //     $this->createFormOptions('isInSitu', ['required' => false])
            // )
        ;
    }
}
