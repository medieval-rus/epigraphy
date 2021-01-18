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
use App\Admin\Models\AdminInterpretationWrapper;
use App\Persistence\Entity\Epigraphy\Interpretation;
use App\Persistence\Repository\Epigraphy\InterpretationRepository;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class InscriptionAdmin extends AbstractEntityAdmin
{
    /**
     * @var string
     */
    protected $baseRouteName = 'epigraphy_inscription';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'epigraphy/inscription';

    public function preUpdate($object): void
    {
        $inscription = $object;
        $zeroRow = $inscription->getZeroRow();
        $interpretations = $inscription->getInterpretations();

        $unwrappedInterpretations = [];

        foreach ($interpretations as $index => $element) {
            if ($element instanceof AdminInterpretationWrapper) {
                $wrapper = $element;
                $interpretation = $wrapper->toInterpretation();

                $unwrappedInterpretations[$index] = $interpretation;

                if (null === $interpretation->getId()) {
                    $wrapper->updateZeroRow($zeroRow);
                }
            }
        }

        foreach ($unwrappedInterpretations as $index => $interpretation) {
            $interpretations->set($index, $interpretation);
        }
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createLabeledListOptions('id'))
            ->add('carrier', null, $this->createLabeledListOptions('carrier'))
            ->add('interpretations', null, $this->createLabeledListOptions('interpretations'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('form.inscription.tab.common.label')
                ->with('form.inscription.section.common.label')
                    ->add(
                        'conventionalDate',
                        null,
                        $this->createLabeledFormOptions('conventionalDate')
                    )
                    ->add(
                        'carrier',
                        ModelType::class,
                        $this->createLabeledFormOptions('carrier', ['required' => true])
                    )
                    ->add(
                        'photos',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('photos')
                    )
                    ->add(
                        'sketches',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('sketches')
                    )
                ->end()
            ->end()
            ->tab('form.inscription.tab.actualResearchInformation.label')
                ->with('form.inscription.section.zeroRowMaterialAspect.label', ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.placeOnCarrier',
                        TextType::class,
                        $this->createLabeledFormOptions('zeroRow.placeOnCarrier', ['required' => false])
                    )
                    ->add(
                        'zeroRow.placeOnCarrierReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('placeOnCarrierReferences')
                    )
                    ->add(
                        'zeroRow.writingTypes',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('zeroRow.writingTypes')
                    )
                    ->add(
                        'zeroRow.writingTypesReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('writingTypesReferences')
                    )
                    ->add(
                        'zeroRow.writingMethods',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('zeroRow.writingMethods')
                    )
                    ->add(
                        'zeroRow.writingMethodsReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('writingMethodsReferences')
                    )
                    ->add(
                        'zeroRow.preservationStates',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('zeroRow.preservationStates')
                    )
                    ->add(
                        'zeroRow.preservationStatesReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('preservationStatesReferences')
                    )
                    ->add(
                        'zeroRow.materials',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('zeroRow.materials')
                    )
                    ->add(
                        'zeroRow.materialsReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('materialsReferences')
                    )
                ->end()
                ->with('form.inscription.section.zeroRowLinguisticAspect.label', ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.alphabets',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('zeroRow.alphabets')
                    )
                    ->add(
                        'zeroRow.alphabetsReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('alphabetsReferences')
                    )
                    ->add(
                        'zeroRow.text',
                        TextareaType::class,
                        $this->createLabeledFormOptions(
                            'zeroRow.text',
                            ['required' => false, 'attr' => ['data-virtual-keyboard' => true]]
                        )
                    )
                    ->add(
                        'zeroRow.textReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('textReferences')
                    )
                    ->add(
                        'zeroRow.textImages',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('zeroRow.textImages')
                    )
                    ->add(
                        'zeroRow.textImagesReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('textImagesReferences')
                    )
                    ->add(
                        'zeroRow.transliteration',
                        TextareaType::class,
                        $this->createLabeledFormOptions('zeroRow.transliteration', ['required' => false])
                    )
                    ->add(
                        'zeroRow.transliterationReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('transliterationReferences')
                    )
                    ->add(
                        'zeroRow.translation',
                        TextareaType::class,
                        $this->createLabeledFormOptions('zeroRow.translation', ['required' => false])
                    )
                    ->add(
                        'zeroRow.translationReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('translationReferences')
                    )
                    ->add(
                        'zeroRow.contentCategories',
                        ModelType::class,
                        $this->createLabeledManyToManyFormOptions('zeroRow.contentCategories')
                    )
                    ->add(
                        'zeroRow.contentCategoriesReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('contentCategoriesReferences')
                    )
                    ->add(
                        'zeroRow.content',
                        TextareaType::class,
                        $this->createLabeledFormOptions('zeroRow.content', ['required' => false])
                    )
                    ->add(
                        'zeroRow.contentReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('contentReferences')
                    )
                ->end()
                ->with('form.inscription.section.zeroRowHistoricalAspect.label', ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.dateInText',
                        TextType::class,
                        $this->createLabeledFormOptions('zeroRow.dateInText', ['required' => false])
                    )
                    ->add(
                        'zeroRow.dateInTextReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('dateInTextReferences')
                    )
                    ->add(
                        'zeroRow.stratigraphicalDate',
                        TextType::class,
                        $this->createLabeledFormOptions('zeroRow.stratigraphicalDate', ['required' => false])
                    )
                    ->add(
                        'zeroRow.stratigraphicalDateReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('stratigraphicalDateReferences')
                    )
                    ->add(
                        'zeroRow.nonStratigraphicalDate',
                        TextType::class,
                        $this->createLabeledFormOptions('zeroRow.nonStratigraphicalDate', ['required' => false])
                    )
                    ->add(
                        'zeroRow.nonStratigraphicalDateReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('nonStratigraphicalDateReferences')
                    )
                    ->add(
                        'zeroRow.historicalDate',
                        TextType::class,
                        $this->createLabeledFormOptions('zeroRow.historicalDate', ['required' => false])
                    )
                    ->add(
                        'zeroRow.historicalDateReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('historicalDateReferences')
                    )
                ->end()
            ->end()
            ->tab('form.inscription.tab.interpretations.label')
                ->with('form.inscription.section.interpretations.label')
                    ->add(
                        'interpretations',
                        CollectionType::class,
                        $this->createLabeledFormOptions(
                            'interpretations',
                            ['required' => false]
                        ),
                        ['edit' => 'inline']
                    )
                ->end()
            ->end()
        ;
    }

    /**
     * @param string $action
     */
    protected function configureTabMenu(ItemInterface $menu, $action, AdminInterface $childAdmin = null): void
    {
        if ('edit' === $action || null !== $childAdmin) {
            $admin = $this->isChild() ? $this->getParent() : $this;

            if ((null !== $inscription = $this->getSubject()) && (null !== ($inscriptionId = $inscription->getId()))) {
                $menu->addChild('tabMenu.siteView', [
                    'uri' => $admin->getRouteGenerator()->generate('inscription__show', [
                        'id' => $inscriptionId,
                    ]),
                ]);
            }
        }
    }

    private function createLabeledReferencesFormOptions(string $fieldName, array $options = []): array
    {
        $subject = $this->getSubject();

        $parentInscriptionId = null === $subject ? null : $subject->getId();

        return $this->createLabeledManyToManyFormOptions(
            'zeroRow.'.$fieldName,
            array_merge(
                $options,
                [
                    'class' => Interpretation::class,
                    'query_builder' => static function (
                        InterpretationRepository $entityRepository
                    ) use ($parentInscriptionId) {
                        $queryBuilder = $entityRepository->createQueryBuilder('interpretation');

                        if (null === $parentInscriptionId) {
                            return $queryBuilder;
                        }

                        return $queryBuilder
                            ->where('interpretation.inscription = :inscriptionId')
                            ->setParameter(':inscriptionId', $parentInscriptionId);
                    },
                    'attr' => [
                        'data-zero-row-references' => $fieldName,
                        'data-sonata-select2' => 'false',
                    ],
                ]
            )
        );
    }
}
