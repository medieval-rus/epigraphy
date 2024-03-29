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
use FOS\CKEditorBundle\Form\Type\CKEditorType;

final class CarrierAdmin extends AbstractEntityAdmin
{
    protected $baseRouteName = 'epigraphy_carrier';

    protected $baseRoutePattern = 'epigraphy/carrier';

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createListOptions('id'))
            ->add('discoverySite', null, $this->createListOptions('discoverySite'))
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
            ->add('characteristics', CKEditorType::class, $this->createFormOptions('characteristics', ['autoload' => false]))
            ->add('materials', null, $this->createManyToManyFormOptions('materials'))
            ->add('materialDescription', CKEditorType::class, $this->createFormOptions('materialDescription', ['autoload' => false, 'required' => false]))
            ->add('stratigraphicalDate', CKEditorType::class, $this->createFormOptions('stratigraphicalDate', ['autoload' => false, 'required' => false]))
            ->add('findCircumstances', CKEditorType::class, $this->createFormOptions('findCircumstances', ['autoload' => false, 'required' => false]))
            // ->add('carrierHistory', CKEditorType::class, $this->createFormOptions('carrierHistory', ['required' => false, 'autoload' => false]))
            ->add('discoverySite', null, $this->createManyToManyFormOptions('discoverySite'))
            // археология
            ->add('archaeology', CKEditorType::class, $this->createFormOptions('archaeology', ['required' => false, 'autoload' => false]))
            // предыдущие места хранения
            ->add('previousStorage', CKEditorType::class, $this->createFormOptions('previousStorage', ['required' => false, 'autoload' => false]))
            ->add('storageSite', null, $this->createManyToManyFormOptions('storageSite'))
            // локализация в месте хранения
            ->add('storageLocalization', CKEditorType::class, $this->createFormOptions('storageLocalization', ['required' => false, 'autoload' => false]))
            ->add('inventoryNumber', null, $this->createFormOptions('inventoryNumber'))
        ;
    }
}
