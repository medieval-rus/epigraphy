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
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

final class CarrierAdmin extends AbstractEntityAdmin
{
    protected string $baseRouteName = 'epigraphy_carrier';

    protected string $baseRoutePattern = 'epigraphy/carrier';

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createLabeledListOptions('id'))
            ->add('origin1', null, $this->createLabeledListOptions('origin1'))
            ->add('origin2', null, $this->createLabeledListOptions('origin2'))
            ->add('individualName', null, $this->createLabeledListOptions('individualName'))
            ->add('storagePlace', null, $this->createLabeledListOptions('storagePlace'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab($this->getTabLabel('identification'))
                ->with($this->getSectionLabel('identification'))
                    ->add('individualName', null, $this->createLabeledFormOptions('individualName'))
                ->end()
            ->end()
            ->tab($this->getTabLabel('classification'))
                ->with($this->getSectionLabel('classification'))
                    ->add('types', null, $this->createLabeledManyToManyFormOptions('types'))
                    ->add('categories', null, $this->createLabeledManyToManyFormOptions('categories'))
                ->end()
            ->end()
            ->tab($this->getTabLabel('origin'))
                ->with($this->getSectionLabel('origin'))
                    ->add('origin1', null, $this->createLabeledFormOptions('origin1'))
                    ->add('origin2', null, $this->createLabeledFormOptions('origin2'))
                    ->add('findCircumstances', null, $this->createLabeledFormOptions('findCircumstances'))
                    ->add('characteristics', null, $this->createLabeledFormOptions('characteristics'))
                ->end()
            ->end()
            ->tab($this->getTabLabel('preservation'))
                ->with($this->getSectionLabel('preservation'))
                    ->add('storagePlace', null, $this->createLabeledFormOptions('storagePlace'))
                    ->add('inventoryNumber', null, $this->createLabeledFormOptions('inventoryNumber'))
                    ->add(
                        'isInSitu',
                        CheckboxType::class,
                        $this->createLabeledFormOptions('isInSitu', ['required' => false])
                    )
                ->end()
            ->end()
        ;
    }
}
