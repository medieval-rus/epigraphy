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
            ->addIdentifier('id', null, $this->createLabeledListOptions('id'))
            ->add('source', null, $this->createLabeledListOptions('source'))
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
                        $this->createLabeledFormOptions('source', ['required' => true])
                    )
                    ->add(
                        'comment',
                        TextareaType::class,
                        $this->createLabeledFormOptions('comment', ['required' => false])
                    )
                ->end()
            ->end()
            ->tab('form.interpretation.tab.bibliographicAspect.label')
                ->with('form.interpretation.section.bibliographicAspect.label')
                    ->add(
                        'pageNumbersInSource',
                        TextType::class,
                        $this->createLabeledFormOptions('pageNumbersInSource', ['required' => false])
                    )
                    ->add(
                        'numberInSource',
                        TextType::class,
                        $this->createLabeledFormOptions('numberInSource', ['required' => false])
                    )
                ->end()
            ->end()
            ->tab('form.interpretation.tab.materialAspect.label')
                ->with('form.interpretation.section.materialAspect.label')
                ->add(
                    'placeOnCarrier',
                    TextType::class,
                    $this->createLabeledFormOptions('placeOnCarrier', ['required' => false])
                )
                ->add(
                    'writingTypes',
                    ModelType::class,
                    $this->createLabeledManyToManyFormOptions('writingTypes')
                )
                ->add(
                    'writingMethods',
                    ModelType::class,
                    $this->createLabeledManyToManyFormOptions('writingMethods')
                )
                ->add(
                    'preservationStates',
                    ModelType::class,
                    $this->createLabeledManyToManyFormOptions('preservationStates')
                )
                ->add(
                    'materials',
                    ModelType::class,
                    $this->createLabeledManyToManyFormOptions('materials')
                )
                ->end()
            ->end()
            ->tab('form.interpretation.tab.linguisticAspect.label')
                ->with('form.interpretation.section.linguisticAspect.label')
                    ->add(
                        'alphabets',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('alphabets')
                    )
                    ->add(
                        'text',
                        TextareaType::class,
                        $this->createLabeledFormOptions('text', ['required' => false])
                    )
                    ->add(
                        'textImageFileNames',
                        TextType::class,
                        $this->createLabeledFormOptions('textImageFileNames', ['required' => false])
                    )
                    ->add(
                        'transliteration',
                        TextareaType::class,
                        $this->createLabeledFormOptions('transliteration', ['required' => false])
                    )
                    ->add(
                        'translation',
                        TextareaType::class,
                        $this->createLabeledFormOptions('translation', ['required' => false])
                    )
                    ->add(
                        'contentCategories',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('contentCategories')
                    )
                    ->add(
                        'content',
                        TextareaType::class,
                        $this->createLabeledFormOptions('content', ['required' => false])
                    )
                ->end()
            ->end()
            ->tab('form.interpretation.tab.historicalAspect.label')
                ->with('form.interpretation.section.historicalAspect.label')
                    ->add(
                        'dateInText',
                        TextType::class,
                        $this->createLabeledFormOptions('dateInText', ['required' => false])
                    )
                    ->add(
                        'stratigraphicalDate',
                        TextType::class,
                        $this->createLabeledFormOptions('stratigraphicalDate', ['required' => false])
                    )
                    ->add(
                        'nonStratigraphicalDate',
                        TextType::class,
                        $this->createLabeledFormOptions('nonStratigraphicalDate', ['required' => false])
                    )
                    ->add(
                        'historicalDate',
                        TextType::class,
                        $this->createLabeledFormOptions('historicalDate', ['required' => false])
                    )
                ->end()
            ->end()
            ->tab('form.interpretation.tab.multimedia.label')
                ->with('form.interpretation.section.multimedia.label')
                    ->add(
                        'photoFileNames',
                        TextType::class,
                        $this->createLabeledFormOptions('photoFileNames', ['required' => false])
                    )
                    ->add(
                        'sketchFileNames',
                        TextType::class,
                        $this->createLabeledFormOptions('sketchFileNames', ['required' => false])
                    )
                ->end()
            ->end()
        ;
    }
}
