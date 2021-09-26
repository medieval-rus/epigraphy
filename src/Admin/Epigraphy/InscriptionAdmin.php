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
use App\Admin\Epigraphy\Models\AdminInterpretationWrapper;
use App\Persistence\Entity\Epigraphy\Inscription;
use App\Persistence\Entity\Epigraphy\Interpretation;
use App\Persistence\Entity\Media\File;
use App\Persistence\Repository\Epigraphy\InterpretationRepository;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class InscriptionAdmin extends AbstractEntityAdmin
{
    protected string $baseRouteName = 'epigraphy_inscription';

    protected string $baseRoutePattern = 'epigraphy/inscription';

    /**
     * @param Inscription $object
     */
    public function preUpdate($object): void
    {
        $inscription = $object;
        $zeroRow = $inscription->getZeroRow();
        $interpretations = $inscription->getInterpretations();

        foreach ($interpretations as $wrapper) {
            if ($wrapper instanceof AdminInterpretationWrapper) {
                $interpretation = $wrapper->toInterpretation();

                if (null === $interpretation->getId()) {
                    $wrapper->updateZeroRow($zeroRow);
                }
            }
        }
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createLabeledListOptions('id'))
            ->addIdentifier('number', null, $this->createLabeledListOptions('number'))
            ->add('carrier', null, $this->createLabeledListOptions('carrier'))
            ->add('interpretations', null, $this->createLabeledListOptions('interpretations'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab($this->getTabLabel('common'))
                ->with($this->getSectionLabel('common'))
                    ->add('number', null, $this->createLabeledFormOptions('number'))
                    ->add('conventionalDate', null, $this->createLabeledFormOptions('conventionalDate'))
                    ->add('carrier', null, $this->createLabeledFormOptions('carrier'))
                    ->add('photos', null, $this->createLabeledManyToManyFormOptions('photos'))
                    ->add('sketches', null, $this->createLabeledManyToManyFormOptions('sketches'))
                    ->add('comment', null, $this->createLabeledFormOptions('comment'))
                ->end()
            ->end()
            ->tab($this->getTabLabel('actualResearchInformation'))
                ->with($this->getSectionLabel('zeroRowMaterialAspect'), ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.placeOnCarrier',
                        TextareaType::class,
                        $this->createLabeledFormOptions('zeroRow.placeOnCarrier')
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
                ->with($this->getSectionLabel('zeroRowLinguisticAspect'), ['class' => 'col-md-6'])
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
                            ['attr' => ['data-virtual-keyboard' => true]]
                        )
                    )
                    ->add(
                        'zeroRow.textReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('textReferences')
                    )
                    ->add(
                        'zeroRow.textImages',
                        EntityType::class,
                        $this->createLabeledManyToManyFormOptions('zeroRow.textImages', ['class' => File::class])
                    )
                    ->add(
                        'zeroRow.textImagesReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('textImagesReferences')
                    )
                    ->add(
                        'zeroRow.transliteration',
                        TextareaType::class,
                        $this->createLabeledFormOptions('zeroRow.transliteration')
                    )
                    ->add(
                        'zeroRow.transliterationReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('transliterationReferences')
                    )
                    ->add(
                        'zeroRow.translation',
                        TextareaType::class,
                        $this->createLabeledFormOptions('zeroRow.translation')
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
                        $this->createLabeledFormOptions('zeroRow.content')
                    )
                    ->add(
                        'zeroRow.contentReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('contentReferences')
                    )
                ->end()
                ->with($this->getSectionLabel('zeroRowHistoricalAspect'), ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.origin',
                        TextareaType::class,
                        $this->createLabeledFormOptions('zeroRow.origin')
                    )
                    ->add(
                        'zeroRow.originReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('originReferences')
                    )
                    ->add(
                        'zeroRow.dateInText',
                        TextareaType::class,
                        $this->createLabeledFormOptions('zeroRow.dateInText')
                    )
                    ->add(
                        'zeroRow.dateInTextReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('dateInTextReferences')
                    )
                    ->add(
                        'zeroRow.stratigraphicalDate',
                        TextareaType::class,
                        $this->createLabeledFormOptions('zeroRow.stratigraphicalDate')
                    )
                    ->add(
                        'zeroRow.stratigraphicalDateReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('stratigraphicalDateReferences')
                    )
                    ->add(
                        'zeroRow.nonStratigraphicalDate',
                        TextareaType::class,
                        $this->createLabeledFormOptions('zeroRow.nonStratigraphicalDate')
                    )
                    ->add(
                        'zeroRow.nonStratigraphicalDateReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('nonStratigraphicalDateReferences')
                    )
                    ->add(
                        'zeroRow.historicalDate',
                        TextareaType::class,
                        $this->createLabeledFormOptions('zeroRow.historicalDate')
                    )
                    ->add(
                        'zeroRow.historicalDateReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('historicalDateReferences')
                    )
                ->end()
            ->end()
            ->tab($this->getTabLabel('interpretations'))
                ->with($this->getSectionLabel('interpretations'))
                    ->add(
                        'interpretations',
                        CollectionType::class,
                        $this->createLabeledFormOptions('interpretations', ['required' => false]),
                        ['edit' => 'inline', 'admin_code' => 'admin.interpretation']
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
                        return $entityRepository
                            ->createQueryBuilder('interpretation')
                            ->where('interpretation.inscription IS NOT NULL')
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
