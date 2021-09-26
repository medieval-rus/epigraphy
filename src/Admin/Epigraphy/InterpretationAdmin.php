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
use App\DataStorage\DataStorageManagerInterface;
use App\Form\DataTransformer\InterpretationAdminTransformer;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

final class InterpretationAdmin extends AbstractEntityAdmin
{
    protected $baseRouteName = 'epigraphy_interpretation';

    protected $baseRoutePattern = 'epigraphy/interpretation';

    private DataStorageManagerInterface $dataStorageManager;

    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        DataStorageManagerInterface $dataStorageManager
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->dataStorageManager = $dataStorageManager;
    }

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
            ->tab($this->getTabLabel('identification'))
                ->with($this->getSectionLabel('identification'))
                    ->add(
                        'id',
                        HiddenType::class,
                        ['attr' => ['data-interpretation-id' => $this->getSubject()->getId()]]
                    )
                    ->add('source', null, $this->createLabeledFormOptions('source'))
                    ->add('comment', null, $this->createLabeledFormOptions('comment'))
                ->end()
            ->end()
            ->tab($this->getTabLabel('bibliographicAspect'))
                ->with($this->getSectionLabel('bibliographicAspect'))
                    ->add('pageNumbersInSource', null, $this->createLabeledFormOptions('pageNumbersInSource'))
                    ->add('numberInSource', null, $this->createLabeledFormOptions('numberInSource'))
                ->end()
            ->end()
            ->tab($this->getTabLabel('materialAspect'))
                ->with($this->getSectionLabel('materialAspect'))
                    ->add('placeOnCarrier', null, $this->createLabeledFormOptions('placeOnCarrier'))
                    ->add(
                        'isPlaceOnCarrierPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('placeOnCarrier')
                    )
                    ->add('writingTypes', null, $this->createLabeledManyToManyFormOptions('writingTypes'))
                    ->add(
                        'isWritingTypesPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('writingTypes')
                    )
                    ->add('writingMethods', null, $this->createLabeledManyToManyFormOptions('writingMethods'))
                    ->add(
                        'isWritingMethodsPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('writingMethods')
                    )
                    ->add('preservationStates', null, $this->createLabeledManyToManyFormOptions('preservationStates'))
                    ->add(
                        'isPreservationStatesPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('preservationStates')
                    )
                    ->add('materials', null, $this->createLabeledManyToManyFormOptions('materials'))
                    ->add(
                        'isMaterialsPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('materials')
                    )
                ->end()
            ->end()
            ->tab($this->getTabLabel('linguisticAspect'))
                ->with($this->getSectionLabel('linguisticAspect'))
                    ->add('alphabets', null, $this->createLabeledManyToManyFormOptions('alphabets'))
                    ->add(
                        'isAlphabetsPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('alphabets')
                    )
                    ->add(
                        'text',
                        null,
                        $this->createLabeledFormOptions('text', ['attr' => ['data-virtual-keyboard' => true]])
                    )
                    ->add(
                        'isTextPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('text')
                    )
                    ->add(
                        'textImages',
                        null,
                        $this->createLabeledManyToManyFormOptions(
                            'textImages',
                            [
                                'choice_filter' => $this->dataStorageManager->getFolderFilter('text'),
                                'query_builder' => $this->dataStorageManager->getQueryBuilder(),
                            ]
                        )
                    )
                    ->add(
                        'isTextImagesPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('textImages')
                    )
                    ->add('transliteration', null, $this->createLabeledFormOptions('transliteration'))
                    ->add(
                        'isTransliterationPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('transliteration')
                    )
                    ->add('translation', null, $this->createLabeledFormOptions('translation'))
                    ->add(
                        'isTranslationPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('translation')
                    )
                    ->add('contentCategories', null, $this->createLabeledManyToManyFormOptions('contentCategories'))
                    ->add(
                        'isContentCategoriesPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('contentCategories')
                    )
                    ->add('content', null, $this->createLabeledFormOptions('content'))
                    ->add(
                        'isContentPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('content')
                    )
                ->end()
            ->end()
            ->tab($this->getTabLabel('historicalAspect'))
                ->with($this->getSectionLabel('historicalAspect'))
                    ->add('origin', null, $this->createLabeledFormOptions('origin'))
                    ->add(
                        'isOriginPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('origin')
                    )
                    ->add('dateInText', null, $this->createLabeledFormOptions('dateInText'))
                    ->add(
                        'isDateInTextPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('dateInText')
                    )
                    ->add('stratigraphicalDate', null, $this->createLabeledFormOptions('stratigraphicalDate'))
                    ->add(
                        'isStratigraphicalDatePartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('stratigraphicalDate')
                    )
                    ->add('nonStratigraphicalDate', null, $this->createLabeledFormOptions('nonStratigraphicalDate'))
                    ->add(
                        'isNonStratigraphicalDatePartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('nonStratigraphicalDate')
                    )
                    ->add('historicalDate', null, $this->createLabeledFormOptions('historicalDate'))
                    ->add(
                        'isHistoricalDatePartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('historicalDate')
                    )
                ->end()
            ->end()
        ;

        $formMapper->getFormBuilder()->addViewTransformer(new InterpretationAdminTransformer());
    }

    private function createLabeledZeroRowPartFormOptions(
        string $zeroRowField,
        array $options = []
    ): array {
        return $this->createLabeledFormOptions(
            'isPartOfZeroRow.'.$zeroRowField,
            array_merge(
                $options,
                [
                    'required' => false,
                    'attr' => [
                        'data-zero-row-part' => $zeroRowField.'References',
                    ],
                ]
            )
        );
    }
}
