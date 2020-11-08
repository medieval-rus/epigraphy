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

namespace App\Admin;

use App\Admin\Abstraction\AbstractEntityAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CarrierAdmin extends AbstractEntityAdmin
{
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createListLabeledOptions('id'))
            ->add('origin1', null, $this->createListLabeledOptions('origin1'))
            ->add('origin2', null, $this->createListLabeledOptions('origin2'))
            ->add('individualName', null, $this->createListLabeledOptions('individualName'))
            ->add('storagePlace', null, $this->createListLabeledOptions('storagePlace'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('form.carrier.tab.identification.label')
                ->with('form.carrier.section.identification.label')
                    ->add(
                        'individualName',
                        TextType::class,
                        $this->createFormLabeledOptions('individualName', ['required' => false])
                    )
                ->end()
            ->end()
            ->tab('form.carrier.tab.classification.label')
                ->with('form.carrier.section.classification.label')
                    ->add(
                        'type',
                        ModelType::class,
                        $this->createFormLabeledOptions('type', ['required' => true])
                    )
                    ->add(
                        'category',
                        ModelType::class,
                        $this->createFormLabeledOptions('category', ['required' => true])
                    )
                ->end()
            ->end()
            ->tab('form.carrier.tab.origin.label')
                ->with('form.carrier.section.origin.label')
                    ->add(
                        'origin1',
                        TextType::class,
                        $this->createFormLabeledOptions('origin1', ['required' => false])
                    )
                    ->add(
                        'origin2',
                        TextType::class,
                        $this->createFormLabeledOptions('origin2', ['required' => false])
                    )
                    ->add(
                        'findCircumstances',
                        TextareaType::class,
                        $this->createFormLabeledOptions('findCircumstances', ['required' => false])
                    )
                    ->add(
                        'characteristics',
                        TextareaType::class,
                        $this->createFormLabeledOptions('characteristics', ['required' => false])
                    )
                ->end()
            ->end()
            ->tab('form.carrier.tab.preservation.label')
                ->with('form.carrier.section.preservation.label')
                    ->add(
                        'storagePlace',
                        TextareaType::class,
                        $this->createFormLabeledOptions('storagePlace', ['required' => false])
                    )
                    ->add(
                        'inventoryNumber',
                        TextType::class,
                        $this->createFormLabeledOptions('inventoryNumber', ['required' => false])
                    )
                    ->add(
                        'isInSitu',
                        CheckboxType::class,
                        $this->createFormLabeledOptions('isInSitu', ['required' => false])
                    )
                ->end()
            ->end()
        ;
    }
}
