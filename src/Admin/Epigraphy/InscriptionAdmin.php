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
use App\Persistence\Entity\Epigraphy\Interpretation;
use App\Persistence\Entity\Media\File;
use Doctrine\ORM\EntityRepository;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class InscriptionAdmin extends AbstractEntityAdmin
{
    protected $baseRouteName = 'epigraphy_inscription';

    protected $baseRoutePattern = 'epigraphy/inscription';

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

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('clone', $this->getRouterIdParameter().'/clone');
    }

    protected function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        if ('edit' === $action) {
            $buttonList['clone'] = ['template' => 'admin/clone_button.html.twig'];
        }

        return $buttonList;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createListOptions('id'))
            ->addIdentifier('number', null, $this->createListOptions('number'))
            ->add('carrier', null, $this->createListOptions('carrier'))
            ->add('interpretations', null, $this->createListOptions('interpretations'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab($this->getTabLabel('common'))
                ->with($this->getSectionLabel('common'))
                    ->add('number', null, $this->createFormOptions('number'))
                    ->add('conventionalDate', null, $this->createFormOptions('conventionalDate'))
                    ->add('carrier', null, $this->createFormOptions('carrier'))
                    ->add('comment', null, $this->createFormOptions('comment'))
                    ->add('isShownOnSite', null, $this->createFormOptions('isShownOnSite'))
                    ->add('isPartOfCorpus', null, $this->createFormOptions('isPartOfCorpus'))
                ->end()
            ->end()
            ->tab($this->getTabLabel('actualResearchInformation'))
                ->with($this->getSectionLabel('zeroRowMaterialAspect'), ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.placeOnCarrier',
                        TextareaType::class,
                        $this->createFormOptions('zeroRow.placeOnCarrier', ['required' => false])
                    )
                    ->add(
                        'zeroRow.placeOnCarrierReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('placeOnCarrierReferences')
                    )
                    ->add(
                        'zeroRow.writingTypes',
                        ModelType::class,
                        $this->createManyToManyFormOptions('zeroRow.writingTypes')
                    )
                    ->add(
                        'zeroRow.writingTypesReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('writingTypesReferences')
                    )
                    ->add(
                        'zeroRow.writingMethods',
                        ModelType::class,
                        $this->createManyToManyFormOptions('zeroRow.writingMethods')
                    )
                    ->add(
                        'zeroRow.writingMethodsReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('writingMethodsReferences')
                    )
                    ->add(
                        'zeroRow.preservationStates',
                        ModelType::class,
                        $this->createManyToManyFormOptions('zeroRow.preservationStates')
                    )
                    ->add(
                        'zeroRow.preservationStatesReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('preservationStatesReferences')
                    )
                    ->add(
                        'zeroRow.materials',
                        ModelType::class,
                        $this->createManyToManyFormOptions('zeroRow.materials')
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
                        $this->createManyToManyFormOptions('zeroRow.alphabets')
                    )
                    ->add(
                        'zeroRow.alphabetsReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('alphabetsReferences')
                    )
                    ->add(
                        'zeroRow.text',
                        TextareaType::class,
                        $this->createFormOptions(
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
                        'zeroRow.transliteration',
                        TextareaType::class,
                        $this->createFormOptions('zeroRow.transliteration', ['required' => false])
                    )
                    ->add(
                        'zeroRow.transliterationReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('transliterationReferences')
                    )
                    ->add(
                        'zeroRow.translation',
                        TextareaType::class,
                        $this->createFormOptions('zeroRow.translation', ['required' => false])
                    )
                    ->add(
                        'zeroRow.translationReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('translationReferences')
                    )
                    ->add(
                        'zeroRow.contentCategories',
                        ModelType::class,
                        $this->createManyToManyFormOptions('zeroRow.contentCategories')
                    )
                    ->add(
                        'zeroRow.contentCategoriesReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('contentCategoriesReferences')
                    )
                    ->add(
                        'zeroRow.description',
                        TextareaType::class,
                        $this->createFormOptions('zeroRow.description', ['required' => false])
                    )
                    ->add(
                        'zeroRow.descriptionReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('descriptionReferences')
                    )
                ->end()
                ->with($this->getSectionLabel('zeroRowHistoricalAspect'), ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.origin',
                        TextareaType::class,
                        $this->createFormOptions('zeroRow.origin', ['required' => false])
                    )
                    ->add(
                        'zeroRow.originReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('originReferences')
                    )
                    ->add(
                        'zeroRow.dateInText',
                        TextareaType::class,
                        $this->createFormOptions('zeroRow.dateInText', ['required' => false])
                    )
                    ->add(
                        'zeroRow.dateInTextReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('dateInTextReferences')
                    )
                    ->add(
                        'zeroRow.stratigraphicalDate',
                        TextareaType::class,
                        $this->createFormOptions('zeroRow.stratigraphicalDate', ['required' => false])
                    )
                    ->add(
                        'zeroRow.stratigraphicalDateReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('stratigraphicalDateReferences')
                    )
                    ->add(
                        'zeroRow.nonStratigraphicalDate',
                        TextareaType::class,
                        $this->createFormOptions('zeroRow.nonStratigraphicalDate', ['required' => false])
                    )
                    ->add(
                        'zeroRow.nonStratigraphicalDateReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('nonStratigraphicalDateReferences')
                    )
                    ->add(
                        'zeroRow.historicalDate',
                        TextareaType::class,
                        $this->createFormOptions('zeroRow.historicalDate', ['required' => false])
                    )
                    ->add(
                        'zeroRow.historicalDateReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('historicalDateReferences')
                    )
                ->end()
                ->with($this->getSectionLabel('zeroRowMedia'), ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.photos',
                        EntityType::class,
                        $this->createManyToManyFormOptions(
                            'zeroRow.photos',
                            [
                                'class' => File::class,
                                'choice_filter' => $this->dataStorageManager->getFolderFilter('photo'),
                                'query_builder' => $this->dataStorageManager->getQueryBuilder(),
                            ]
                        )
                    )
                    ->add(
                        'zeroRow.photosReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('photosReferences')
                    )
                    ->add(
                        'zeroRow.drawings',
                        EntityType::class,
                        $this->createManyToManyFormOptions(
                            'zeroRow.drawings',
                            [
                                'class' => File::class,
                                'choice_filter' => $this->dataStorageManager->getFolderFilter('drawing'),
                                'query_builder' => $this->dataStorageManager->getQueryBuilder(),
                            ]
                        )
                    )
                    ->add(
                        'zeroRow.drawingsReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('drawingsReferences')
                    )
                    ->add(
                        'zeroRow.textImages',
                        EntityType::class,
                        $this->createManyToManyFormOptions(
                            'zeroRow.textImages',
                            [
                                'class' => File::class,
                                'choice_filter' => $this->dataStorageManager->getFolderFilter('text'),
                                'query_builder' => $this->dataStorageManager->getQueryBuilder(),
                            ]
                        )
                    )
                    ->add(
                        'zeroRow.textImagesReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('textImagesReferences')
                    )
                ->end()
            ->end()
            ->tab($this->getTabLabel('interpretations'))
                ->with($this->getSectionLabel('interpretations'))
                    ->add(
                        'interpretations',
                        CollectionType::class,
                        $this->createFormOptions('interpretations', ['required' => false]),
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
                $menu->addChild(
                    'tabMenu.inscription.viewOnSite',
                    [
                        'uri' => $admin->getRouteGenerator()->generate('inscription__show', ['id' => $inscriptionId]),
                    ]
                );
            }
        }
    }

    private function createLabeledReferencesFormOptions(string $fieldName, array $options = []): array
    {
        $subject = $this->getSubject();

        $parentInscriptionId = null === $subject ? null : $subject->getId();

        return $this->createManyToManyFormOptions(
            'zeroRow.'.$fieldName,
            array_merge(
                $options,
                [
                    'class' => Interpretation::class,
                    'query_builder' => static function (EntityRepository $entityRepository) use ($parentInscriptionId) {
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
