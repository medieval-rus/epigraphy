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
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createListLabeledOptions('id'))
            ->add('carrier', null, $this->createListLabeledOptions('carrier'))
            ->add('interpretations', null, $this->createListLabeledOptions('interpretations'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $parentInscriptionId = $formMapper->getAdmin()->getSubject()->getId();

        $referenceOptions = [
            'required' => false,
            'multiple' => true,
            'class' => Interpretation::class,
            'query_builder' => static function (InterpretationRepository $entityRepository) use ($parentInscriptionId) {
                return $entityRepository
                    ->createQueryBuilder('interpretation')
                    ->where('interpretation.inscription = :inscriptionId')
                    ->setParameter(':inscriptionId', $parentInscriptionId);
            },
        ];

        $formMapper
            ->tab('form.inscription.tab.carrier.label')
                ->with('form.inscription.section.carrier.label')
                    ->add(
                        'carrier',
                        ModelType::class,
                        $this->createFormLabeledOptions('carrier', ['required' => true])
                    )
                ->end()
            ->end()
            ->tab('form.inscription.tab.actualResearchInformation.label')
                ->with('form.inscription.section.zeroRowMaterialAspect.label', ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.placeOnCarrier',
                        TextType::class,
                        $this->createFormLabeledOptions('zeroRow.placeOnCarrier', ['required' => false])
                    )
                    ->add(
                        'zeroRow.placeOnCarrierReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.placeOnCarrierReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.writingType',
                        ModelType::class,
                        $this->createFormLabeledOptions('zeroRow.writingType', ['required' => false])
                    )
                    ->add(
                        'zeroRow.writingTypeReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.writingTypeReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.writingMethod',
                        ModelType::class,
                        $this->createFormLabeledOptions('zeroRow.writingMethod', ['required' => false])
                    )
                    ->add(
                        'zeroRow.writingMethodReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.writingMethodReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.preservationState',
                        ModelType::class,
                        $this->createFormLabeledOptions('zeroRow.preservationState', ['required' => false])
                    )
                    ->add(
                        'zeroRow.preservationStateReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.preservationStateReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.materials',
                        ModelType::class,
                        $this->createFormLabeledOptions('zeroRow.materials', ['required' => false, 'multiple' => true])
                    )
                    ->add(
                        'zeroRow.materialsReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.materialsReferences', $referenceOptions)
                    )
                ->end()
                ->with('form.inscription.section.zeroRowLinguisticAspect.label', ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.alphabet',
                        ModelType::class,
                        $this->createFormLabeledOptions('zeroRow.alphabet', ['required' => false])
                    )
                    ->add(
                        'zeroRow.alphabetReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.alphabetReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.text',
                        TextareaType::class,
                        $this->createFormLabeledOptions('zeroRow.text', ['required' => false])
                    )
                    ->add(
                        'zeroRow.textReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.textReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.textImageFileNames',
                        TextType::class,
                        $this->createFormLabeledOptions('zeroRow.textImageFileNames', ['required' => false])
                    )
                    ->add(
                        'zeroRow.textImageFileNamesReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.textImageFileNamesReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.transliteration',
                        TextareaType::class,
                        $this->createFormLabeledOptions('zeroRow.transliteration', ['required' => false])
                    )
                    ->add(
                        'zeroRow.transliterationReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.transliterationReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.translation',
                        TextareaType::class,
                        $this->createFormLabeledOptions('zeroRow.translation', ['required' => false])
                    )
                    ->add(
                        'zeroRow.translationReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.translationReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.contentCategory',
                        ModelType::class,
                        $this->createFormLabeledOptions('zeroRow.contentCategory', ['required' => false])
                    )
                    ->add(
                        'zeroRow.contentCategoryReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.contentCategoryReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.content',
                        TextareaType::class,
                        $this->createFormLabeledOptions('zeroRow.content', ['required' => false])
                    )
                    ->add(
                        'zeroRow.contentReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.contentReferences', $referenceOptions)
                    )
                ->end()
                ->with('form.inscription.section.zeroRowHistoricalAspect.label', ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.dateInText',
                        TextType::class,
                        $this->createFormLabeledOptions('zeroRow.dateInText', ['required' => false])
                    )
                    ->add(
                        'zeroRow.dateInTextReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.dateInTextReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.stratigraphicalDate',
                        TextType::class,
                        $this->createFormLabeledOptions('zeroRow.stratigraphicalDate', ['required' => false])
                    )
                    ->add(
                        'zeroRow.stratigraphicalDateReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.stratigraphicalDateReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.nonStratigraphicalDate',
                        TextType::class,
                        $this->createFormLabeledOptions('zeroRow.nonStratigraphicalDate', ['required' => false])
                    )
                    ->add(
                        'zeroRow.nonStratigraphicalDateReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.nonStratigraphicalDateReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.historicalDate',
                        TextType::class,
                        $this->createFormLabeledOptions('zeroRow.historicalDate', ['required' => false])
                    )
                    ->add(
                        'zeroRow.historicalDateReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.historicalDateReferences', $referenceOptions)
                    )
                ->end()
                ->with('form.inscription.section.zeroRowMultimedia.label', ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.photoFileNames',
                        TextType::class,
                        $this->createFormLabeledOptions('zeroRow.photoFileNames', ['required' => false])
                    )
                    ->add(
                        'zeroRow.photoFileNamesReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.photoFileNamesReferences', $referenceOptions)
                    )
                    ->add(
                        'zeroRow.sketchFileNames',
                        TextType::class,
                        $this->createFormLabeledOptions('zeroRow.sketchFileNames', ['required' => false])
                    )
                    ->add(
                        'zeroRow.sketchFileNamesReferences',
                        EntityType::class,
                        $this->createFormLabeledOptions('zeroRow.sketchFileNamesReferences', $referenceOptions)
                    )
                ->end()
            ->end()
            ->tab('form.inscription.tab.interpretations.label')
                ->with('form.inscription.section.interpretations.label')
                    ->add(
                        'interpretations',
                        CollectionType::class,
                        $this->createFormLabeledOptions(
                            'interpretations',
                            ['required' => true, 'by_reference' => false]
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
}
