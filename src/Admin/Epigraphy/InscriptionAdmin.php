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
use FOS\CKEditorBundle\Form\Type\CKEditorType;

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
                    ->add('conventionalDate', null, $this->createFormOptions('conventionalDate'))
                    ->add('dateExplanation', CKEditorType::class, $this->createFormOptions('dateExplanation', ['autoload' => false, 'required' => false]))
                    ->add('carrier', null, $this->createFormOptions('carrier'))
                    ->add('comment', CKEditorType::class, $this->createFormOptions('comment', ['autoload' => false, 'required' => false]))
                    ->add('rssdaRender', null, $this->createFormOptions('rssdaRender'))
                    ->add('isShownOnSite', null, $this->createFormOptions('isShownOnSite'))
                    ->add('isPartOfCorpus', null, $this->createFormOptions('isPartOfCorpus'))
                ->end()
            ->end()
            ->tab($this->getTabLabel('actualResearchInformation'))
                ->with($this->getSectionLabel('zeroRowMaterialAspect'), ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.placeOnCarrier',
                        CKEditorType::class,
                        $this->createFormOptions('zeroRow.placeOnCarrier', ['autoload' => false, 'required' => false])
                    )
                    ->add(
                        'zeroRow.placeOnCarrierReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('placeOnCarrierReferences')
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
                        CKEditorType::class,
                        $this->createFormOptions(
                            'zeroRow.text',
                            ['autoload' => false, 'required' => false, 'config_name' => 'textconfig']
                        )
                    )
                    ->add(
                        'zeroRow.textReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('textReferences')
                    )
                    ->add(
                        'zeroRow.interpretationComment',
                        CKEditorType::class,
                        $this->createFormOptions(
                            'zeroRow.interpretationComment',
                            ['autoload' => false, 'required' => false]
                        )
                    )
                    ->add(
                        'zeroRow.interpretationCommentReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('interpretationCommentReferences')
                    )
                    ->add(
                        'zeroRow.transliteration',
                        CKEditorType::class,
                        $this->createFormOptions('zeroRow.transliteration', ['autoload' => false, 'required' => false, 'config_name' => 'textconfig'])
                    )
                    ->add(
                        'zeroRow.transliterationReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('transliterationReferences')
                    )
                    ->add(
                        'zeroRow.reconstruction',
                        CKEditorType::class,
                        $this->createFormOptions('zeroRow.reconstruction', ['autoload' => false, 'required' => false, 'config_name' => 'textconfig'])
                    )
                    ->add(
                        'zeroRow.reconstructionReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('reconstructionReferences')
                    )
                    ->add(
                        'zeroRow.normalization',
                        CKEditorType::class,
                        $this->createFormOptions('zeroRow.normalization', ['autoload' => false, 'required' => false, 'config_name' => 'textconfig'])
                    )
                    ->add(
                        'zeroRow.normalizationReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('normalizationReferences')
                    )
                    ->add(
                        'zeroRow.translation',
                        CKEditorType::class,
                        $this->createFormOptions('zeroRow.translation', ['autoload' => false, 'required' => false])
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
                        CKEditorType::class,
                        $this->createFormOptions('zeroRow.description', ['autoload' => false, 'required' => false, 'config_name' => 'textconfig'])
                    )
                    ->add(
                        'zeroRow.descriptionReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('descriptionReferences')
                    )
                ->end()
                ->with($this->getSectionLabel('zeroRowHistoricalAspect'), ['class' => 'col-md-6'])
                    ->add(
                        'zeroRow.dateInText',
                        CKEditorType::class,
                        $this->createFormOptions('zeroRow.dateInText', ['autoload' => false, 'required' => false])
                    )
                    ->add(
                        'zeroRow.dateInTextReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('dateInTextReferences')
                    )
                    ->add(
                        'zeroRow.nonStratigraphicalDate',
                        CKEditorType::class,
                        $this->createFormOptions('zeroRow.nonStratigraphicalDate', ['autoload' => false, 'required' => false])
                    )
                    ->add(
                        'zeroRow.nonStratigraphicalDateReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('nonStratigraphicalDateReferences')
                    )
                    ->add(
                        'zeroRow.historicalDate',
                        CKEditorType::class,
                        $this->createFormOptions('zeroRow.historicalDate', ['autoload' => false, 'required' => false])
                    )
                    ->add(
                        'zeroRow.historicalDateReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('historicalDateReferences')
                    )
                    ->add(
                        'zeroRow.origin',
                        CKEditorType::class,
                        $this->createFormOptions(
                            'zeroRow.origin',
                            ['required' => false, 'autoload' => false]
                        )
                    )
                    ->add(
                        'zeroRow.originReferences',
                        EntityType::class,
                        $this->createLabeledReferencesFormOptions('originReferences')
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
                ->end()
            ->end()
            ->tab($this->getTabLabel('interpretations'))
                ->with($this->getSectionLabel('interpretations'))
                    ->add(
                        'interpretations',
                        CollectionType::class,
                        $this->createFormOptions('interpretations', ['required' => false]),
                        [
                            'edit' => 'inline',
                            'admin_code' => 'admin.interpretation',
                            'query_builder' => function (EntityRepository $entityRepository) {
                                return $entityRepository
                                    ->createQueryBuilder('interp')
                                    ->leftJoin('interp.source', 'src')
                                    ->orderBy('src.year', 'DESC');
                            }
                        ]
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
                            ->innerJoin('interpretation.source', 'src')
                            ->where('interpretation.inscription IS NOT NULL')
                            ->where('interpretation.inscription = :inscriptionId')
                            ->setParameter(':inscriptionId', $parentInscriptionId)
                            ->orderBy('src.year', 'DESC');
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
