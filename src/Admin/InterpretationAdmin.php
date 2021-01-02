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
use App\Persistence\Entity\Epigraphy\Interpretation;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class InterpretationAdmin extends AbstractEntityAdmin
{
    /**
     * @var string
     */
    protected $baseRouteName = 'epigraphy_interpretation';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'epigraphy/interpretation';

    public function wrapInterpretation(PreSetDataEvent $event): void
    {
        if (($interpretation = $event->getData()) instanceof Interpretation &&
            !$interpretation instanceof AdminInterpretationWrapper
        ) {
            $event->setData(new AdminInterpretationWrapper($interpretation));
        }
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
            ->getFormBuilder()
            ->getEventDispatcher()
            ->addListener(FormEvents::PRE_SET_DATA, [$this, 'wrapInterpretation']);

        $subject = $this->getSubject();

        if ($subject instanceof AdminInterpretationWrapper) {
            $this->setSubject($subject->toInterpretation());
        }

        $formMapper
            ->tab('form.interpretation.tab.identification.label')
                ->with('form.interpretation.section.identification.label')
                    ->add(
                        'id',
                        HiddenType::class,
                        ['attr' => ['data-interpretation-id' => $this->getSubject()->getId()]]
                    )
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
                        'isPlaceOnCarrierPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('placeOnCarrier')
                    )
                    ->add(
                        'writingTypes',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('writingTypes')
                    )
                    ->add(
                        'isWritingTypesPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('writingTypes')
                    )
                    ->add(
                        'writingMethods',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('writingMethods')
                    )
                    ->add(
                        'isWritingMethodsPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('writingMethods')
                    )
                    ->add(
                        'preservationStates',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('preservationStates')
                    )
                    ->add(
                        'isPreservationStatesPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('preservationStates')
                    )
                    ->add(
                        'materials',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('materials')
                    )
                    ->add(
                        'isMaterialsPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('materials')
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
                        'isAlphabetsPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('alphabets')
                    )
                    ->add(
                        'text',
                        TextareaType::class,
                        $this->createLabeledFormOptions(
                            'text',
                            ['required' => false, 'attr' => ['data-virtual-keyboard' => true]]
                        )
                    )
                    ->add(
                        'isTextPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('text')
                    )
                    ->add(
                        'textImages',
                        TextType::class,
                        $this->createLabeledFormOptions('textImages', ['required' => false])
                    )
                    ->add(
                        'isTextImagesPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('textImages')
                    )
                    ->add(
                        'transliteration',
                        TextareaType::class,
                        $this->createLabeledFormOptions('transliteration', ['required' => false])
                    )
                    ->add(
                        'isTransliterationPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('transliteration')
                    )
                    ->add(
                        'translation',
                        TextareaType::class,
                        $this->createLabeledFormOptions('translation', ['required' => false])
                    )
                    ->add(
                        'isTranslationPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('translation')
                    )
                    ->add(
                        'contentCategories',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('contentCategories')
                    )
                    ->add(
                        'isContentCategoriesPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('contentCategories')
                    )
                    ->add(
                        'content',
                        TextareaType::class,
                        $this->createLabeledFormOptions('content', ['required' => false])
                    )
                    ->add(
                        'isContentPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('content')
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
                        'isDateInTextPartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('dateInText')
                    )
                    ->add(
                        'stratigraphicalDate',
                        TextType::class,
                        $this->createLabeledFormOptions('stratigraphicalDate', ['required' => false])
                    )
                    ->add(
                        'isStratigraphicalDatePartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('stratigraphicalDate')
                    )
                    ->add(
                        'nonStratigraphicalDate',
                        TextType::class,
                        $this->createLabeledFormOptions('nonStratigraphicalDate', ['required' => false])
                    )
                    ->add(
                        'isNonStratigraphicalDatePartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('nonStratigraphicalDate')
                    )
                    ->add(
                        'historicalDate',
                        TextType::class,
                        $this->createLabeledFormOptions('historicalDate', ['required' => false])
                    )
                    ->add(
                        'isHistoricalDatePartOfZeroRow',
                        CheckboxType::class,
                        $this->createLabeledZeroRowPartFormOptions('historicalDate')
                    )
                ->end()
            ->end()
        ;

        if ($subject instanceof AdminInterpretationWrapper) {
            $this->setSubject($subject);
        }
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
