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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class InterpretationAdmin extends AbstractEntityAdmin
{
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createListLabeledOptions('id'))
            ->add('source', null, $this->createListLabeledOptions('source'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('form.interpretation.tab.identification.label')
                ->with('form.interpretation.section.identification.label')
                    ->add(
                        'source',
                        TextType::class,
                        $this->createFormLabeledOptions('source', ['required' => true])
                    )
                    ->add(
                        'comment',
                        TextareaType::class,
                        $this->createFormLabeledOptions('comment', ['required' => false])
                    )
                ->end()
            ->end()
            ->tab('form.interpretation.tab.bibliographicAspect.label')
                ->with('form.interpretation.section.bibliographicAspect.label')
                    ->add(
                        'pageNumbersInSource',
                        TextType::class,
                        $this->createFormLabeledOptions('pageNumbersInSource', ['required' => false])
                    )
                    ->add(
                        'numberInSource',
                        TextType::class,
                        $this->createFormLabeledOptions('numberInSource', ['required' => false])
                    )
                ->end()
            ->end()
            ->tab('form.interpretation.tab.materialAspect.label')
                ->with('form.interpretation.section.materialAspect.label')
                ->add(
                    'placeOnCarrier',
                    TextType::class,
                    $this->createFormLabeledOptions('placeOnCarrier', ['required' => false])
                )
                ->add(
                    'writingType',
                    ModelType::class,
                    $this->createFormLabeledOptions('writingType', ['required' => false])
                )
                ->add(
                    'writingMethod',
                    ModelType::class,
                    $this->createFormLabeledOptions('writingMethod', ['required' => false])
                )
                ->add(
                    'preservationState',
                    ModelType::class,
                    $this->createFormLabeledOptions('preservationState', ['required' => false])
                )
                ->add(
                    'materials',
                    ModelType::class,
                    $this->createFormLabeledOptions('materials', ['required' => false, 'multiple' => true])
                )
                ->end()
            ->end()
            ->tab('form.interpretation.tab.linguisticAspect.label')
                ->with('form.interpretation.section.linguisticAspect.label')
                    ->add(
                        'alphabet',
                        ModelType::class,
                        $this->createFormLabeledOptions('alphabet', ['required' => false])
                    )
                    ->add(
                        'text',
                        TextareaType::class,
                        $this->createFormLabeledOptions('text', ['required' => false])
                    )
                    ->add(
                        'textImageFileNames',
                        TextType::class,
                        $this->createFormLabeledOptions('textImageFileNames', ['required' => false])
                    )
                    ->add(
                        'transliteration',
                        TextareaType::class,
                        $this->createFormLabeledOptions('transliteration', ['required' => false])
                    )
                    ->add(
                        'translation',
                        TextareaType::class,
                        $this->createFormLabeledOptions('translation', ['required' => false])
                    )
                    ->add(
                        'contentCategory',
                        ModelType::class,
                        $this->createFormLabeledOptions('contentCategory', ['required' => false])
                    )
                    ->add(
                        'content',
                        TextareaType::class,
                        $this->createFormLabeledOptions('content', ['required' => false])
                    )
                ->end()
            ->end()
            ->tab('form.interpretation.tab.historicalAspect.label')
                ->with('form.interpretation.section.historicalAspect.label')
                    ->add(
                        'dateInText',
                        TextType::class,
                        $this->createFormLabeledOptions('dateInText', ['required' => false])
                    )
                    ->add(
                        'stratigraphicalDate',
                        TextType::class,
                        $this->createFormLabeledOptions('stratigraphicalDate', ['required' => false])
                    )
                    ->add(
                        'nonStratigraphicalDate',
                        TextType::class,
                        $this->createFormLabeledOptions('nonStratigraphicalDate', ['required' => false])
                    )
                    ->add(
                        'historicalDate',
                        TextType::class,
                        $this->createFormLabeledOptions('historicalDate', ['required' => false])
                    )
                ->end()
            ->end()
            ->tab('form.interpretation.tab.multimedia.label')
                ->with('form.interpretation.section.multimedia.label')
                    ->add(
                        'photoFileNames',
                        TextType::class,
                        $this->createFormLabeledOptions('photoFileNames', ['required' => false])
                    )
                    ->add(
                        'sketchFileNames',
                        TextType::class,
                        $this->createFormLabeledOptions('sketchFileNames', ['required' => false])
                    )
                ->end()
            ->end()
        ;
    }
}
