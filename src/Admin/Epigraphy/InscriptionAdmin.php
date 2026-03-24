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
use App\Persistence\Entity\Epigraphy\LocalizedText;
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
use FOS\CKEditorBundle\Form\Type\CKEditorType;

final class InscriptionAdmin extends AbstractEntityAdmin
{
    /** @var array<string, ?LocalizedText> */
    private array $localizedTextEntityCache = [];

    private const INSCRIPTION_TRANSLATABLE_FIELDS = [
        'dateExplanation' => CKEditorType::class,
        'comment' => CKEditorType::class,
    ];

    private const ZERO_ROW_TRANSLATABLE_FIELDS = [
        'origin' => CKEditorType::class,
        'placeOnCarrier' => CKEditorType::class,
        'interpretationComment' => CKEditorType::class,
        'text' => CKEditorType::class,
        'transliteration' => CKEditorType::class,
        'reconstruction' => CKEditorType::class,
        'normalization' => CKEditorType::class,
        'translation' => CKEditorType::class,
        'description' => CKEditorType::class,
        'dateInText' => CKEditorType::class,
        'nonStratigraphicalDate' => CKEditorType::class,
        'historicalDate' => CKEditorType::class,
    ];

    private const INTERPRETATION_TRANSLATABLE_FIELDS = [
        'comment' => CKEditorType::class,
        'origin' => CKEditorType::class,
        'placeOnCarrier' => CKEditorType::class,
        'interpretationComment' => CKEditorType::class,
        'text' => CKEditorType::class,
        'transliteration' => CKEditorType::class,
        'reconstruction' => CKEditorType::class,
        'normalization' => CKEditorType::class,
        'translation' => CKEditorType::class,
        'description' => CKEditorType::class,
        'dateInText' => CKEditorType::class,
        'nonStratigraphicalDate' => CKEditorType::class,
        'historicalDate' => CKEditorType::class,
    ];

    /** @var string[] */
    private const TEXTCONFIG_ZERO_ROW_FIELDS = [
        'text',
        'transliteration',
        'reconstruction',
        'normalization',
        'description',
    ];

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
            $buttonList['translateAllInscription'] = ['template' => 'admin/translate_all_inscription_button.html.twig'];
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
                    ->add(
                        $this->getTranslationFieldName(LocalizedText::TARGET_INSCRIPTION, 'dateExplanation'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_INSCRIPTION, 'dateExplanation')
                    )
                    ->add('carrier', null, $this->createFormOptions('carrier'))
                    ->add('comment', CKEditorType::class, $this->createFormOptions('comment', ['autoload' => false, 'required' => false]))
                    ->add(
                        $this->getTranslationFieldName(LocalizedText::TARGET_INSCRIPTION, 'comment'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_INSCRIPTION, 'comment')
                    )
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
                        $this->getTranslationFieldName(LocalizedText::TARGET_ZERO_ROW, 'placeOnCarrier'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_ZERO_ROW, 'placeOnCarrier')
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
                        $this->getTranslationFieldName(LocalizedText::TARGET_ZERO_ROW, 'text'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_ZERO_ROW, 'text')
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
                        $this->getTranslationFieldName(LocalizedText::TARGET_ZERO_ROW, 'interpretationComment'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_ZERO_ROW, 'interpretationComment')
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
                        $this->getTranslationFieldName(LocalizedText::TARGET_ZERO_ROW, 'transliteration'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_ZERO_ROW, 'transliteration')
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
                        $this->getTranslationFieldName(LocalizedText::TARGET_ZERO_ROW, 'reconstruction'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_ZERO_ROW, 'reconstruction')
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
                        $this->getTranslationFieldName(LocalizedText::TARGET_ZERO_ROW, 'normalization'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_ZERO_ROW, 'normalization')
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
                        $this->getTranslationFieldName(LocalizedText::TARGET_ZERO_ROW, 'translation'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_ZERO_ROW, 'translation')
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
                        $this->getTranslationFieldName(LocalizedText::TARGET_ZERO_ROW, 'description'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_ZERO_ROW, 'description')
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
                        $this->getTranslationFieldName(LocalizedText::TARGET_ZERO_ROW, 'dateInText'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_ZERO_ROW, 'dateInText')
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
                        $this->getTranslationFieldName(LocalizedText::TARGET_ZERO_ROW, 'nonStratigraphicalDate'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_ZERO_ROW, 'nonStratigraphicalDate')
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
                        $this->getTranslationFieldName(LocalizedText::TARGET_ZERO_ROW, 'historicalDate'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_ZERO_ROW, 'historicalDate')
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
                        $this->getTranslationFieldName(LocalizedText::TARGET_ZERO_ROW, 'origin'),
                        CKEditorType::class,
                        $this->createTranslationFieldOptions(LocalizedText::TARGET_ZERO_ROW, 'origin')
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

    public function postPersist($object): void
    {
        $this->storeLocalizedTexts($object);
    }

    public function postUpdate($object): void
    {
        $this->storeLocalizedTexts($object);
    }

    private function getTranslationFieldName(string $targetType, string $fieldName): string
    {
        return 'localizedEn__'.$targetType.'__'.$fieldName;
    }

    private function createTranslationFieldOptions(string $targetType, string $fieldName): array
    {
        $fields = [
            LocalizedText::TARGET_INSCRIPTION => self::INSCRIPTION_TRANSLATABLE_FIELDS,
            LocalizedText::TARGET_ZERO_ROW => self::ZERO_ROW_TRANSLATABLE_FIELDS,
        ];

        $sourceFieldSuffix = sprintf('[%s]', $fieldName);
        if (LocalizedText::TARGET_ZERO_ROW === $targetType) {
            $sourceFieldSuffix = sprintf('[zeroRow__%s]', $fieldName);
        }

        $options = [
            'mapped' => false,
            'required' => false,
            'label' => sprintf('%s (EN)', $fieldName),
            'data' => $this->getLocalizedTextValue($targetType, $fieldName),
            'attr' => [
                'data-auto-translate-ai-generated' => $this->isLocalizedTextAiGenerated($targetType, $fieldName) ? '1' : '0',
                'data-auto-translate-target-type' => $targetType,
                'data-auto-translate-target-id' => (string) ($this->getTargetIdByType($targetType) ?? ''),
                'data-auto-translate-target-field' => $fieldName,
                'data-auto-translate-target-locale' => 'en',
            ],
        ];

        if ('text' !== $fieldName) {
            $options['attr']['data-auto-translate-source-suffix'] = $sourceFieldSuffix;
            $options['attr']['data-auto-translate-target-lang'] = 'en';
            $options['attr']['data-auto-translate-source-lang'] = 'ru';
        }

        if (CKEditorType::class === $fields[$targetType][$fieldName]) {
            $options['autoload'] = false;
            if (
                LocalizedText::TARGET_ZERO_ROW === $targetType
                && in_array($fieldName, self::TEXTCONFIG_ZERO_ROW_FIELDS, true)
            ) {
                $options['config_name'] = 'textconfig';
            }
        }

        return $options;
    }

    private function getTargetIdByType(string $targetType): ?int
    {
        $subject = $this->getSubject();
        if (null === $subject) {
            return null;
        }

        if (LocalizedText::TARGET_INSCRIPTION === $targetType) {
            return $subject->getId();
        }

        if (LocalizedText::TARGET_ZERO_ROW === $targetType && null !== $subject->getZeroRow()) {
            return $subject->getZeroRow()->getId();
        }

        return null;
    }

    private function getLocalizedTextValue(string $targetType, string $fieldName): ?string
    {
        $localizedText = $this->getLocalizedTextEntity($targetType, $fieldName);

        return null === $localizedText ? null : $localizedText->getValue();
    }

    private function isLocalizedTextAiGenerated(string $targetType, string $fieldName): bool
    {
        $localizedText = $this->getLocalizedTextEntity($targetType, $fieldName);

        if (null === $localizedText) {
            return false;
        }

        return $localizedText->isAiGenerated();
    }

    private function getLocalizedTextEntity(string $targetType, string $fieldName): ?LocalizedText
    {
        $targetId = $this->getTargetIdByType($targetType);
        if (null === $targetId) {
            return null;
        }

        $cacheKey = sprintf('%s|%d|%s|en', $targetType, (int) $targetId, $fieldName);
        if (array_key_exists($cacheKey, $this->localizedTextEntityCache)) {
            return $this->localizedTextEntityCache[$cacheKey];
        }

        $entity = $this->getModelManager()->getEntityManager(LocalizedText::class);
        $localizedText = $entity->getRepository(LocalizedText::class)->findOneBy(
            [
                'targetType' => $targetType,
                'targetId' => (int) $targetId,
                'field' => $fieldName,
                'locale' => 'en',
            ]
        );
        $this->localizedTextEntityCache[$cacheKey] = $localizedText;

        return $localizedText;
    }

    private function storeLocalizedTexts($inscription): void
    {
        if (null === $inscription || null === $inscription->getId()) {
            return;
        }

        $targetIds = [
            LocalizedText::TARGET_INSCRIPTION => $inscription->getId(),
            LocalizedText::TARGET_ZERO_ROW => null === $inscription->getZeroRow() ? null : $inscription->getZeroRow()->getId(),
        ];

        $fieldsByTarget = [
            LocalizedText::TARGET_INSCRIPTION => self::INSCRIPTION_TRANSLATABLE_FIELDS,
            LocalizedText::TARGET_ZERO_ROW => self::ZERO_ROW_TRANSLATABLE_FIELDS,
        ];

        $entity = $this->getModelManager()->getEntityManager(LocalizedText::class);
        $repository = $entity->getRepository(LocalizedText::class);
        $form = $this->getForm();
        $aiFlags = $this->extractAiFlagsFromRequest();

        foreach ($fieldsByTarget as $targetType => $fields) {
            $targetId = $targetIds[$targetType];
            if (null === $targetId) {
                continue;
            }

            foreach (array_keys($fields) as $fieldName) {
                $formFieldName = $this->getSubmittedTranslationFieldName($targetType, $fieldName);
                if (!$form->has($formFieldName)) {
                    continue;
                }

                $value = $form->get($formFieldName)->getData();
                $trimmedValue = null === $value ? null : trim((string) $value);

                $localizedText = $repository->findOneBy(
                    [
                        'targetType' => $targetType,
                        'targetId' => (int) $targetId,
                        'field' => $fieldName,
                        'locale' => 'en',
                    ]
                );

                if (null === $trimmedValue || '' === $trimmedValue) {
                    if (null !== $localizedText) {
                        $entity->remove($localizedText);
                    }
                    continue;
                }

                if (null === $localizedText) {
                    $localizedText = (new LocalizedText())
                        ->setTargetType($targetType)
                        ->setTargetId((int) $targetId)
                        ->setField($fieldName)
                        ->setLocale('en');
                    $entity->persist($localizedText);
                }

                $localizedText->setValue($trimmedValue);
                $localizedText->setIsAiGenerated(
                    $this->resolveAiGeneratedFlag($aiFlags, $formFieldName, $localizedText->isAiGenerated())
                );
            }
        }

        if ($form->has('interpretations')) {
            foreach ($form->get('interpretations') as $interpretationFormIndex => $interpretationForm) {
                $interpretation = $interpretationForm->getData();

                if (!$interpretation instanceof Interpretation || null === $interpretation->getId()) {
                    continue;
                }

                $interpretationAiFlags = $aiFlags['interpretations'][$interpretationFormIndex] ?? [];
                if (!is_array($interpretationAiFlags)) {
                    $interpretationAiFlags = [];
                }

                foreach (array_keys(self::INTERPRETATION_TRANSLATABLE_FIELDS) as $fieldName) {
                    $formFieldName = $this->getSubmittedTranslationFieldName(LocalizedText::TARGET_INTERPRETATION, $fieldName);
                    if (!$interpretationForm->has($formFieldName)) {
                        continue;
                    }

                    $value = $interpretationForm->get($formFieldName)->getData();
                    $trimmedValue = null === $value ? null : trim((string) $value);

                    $localizedText = $repository->findOneBy(
                        [
                            'targetType' => LocalizedText::TARGET_INTERPRETATION,
                            'targetId' => (int) $interpretation->getId(),
                            'field' => $fieldName,
                            'locale' => 'en',
                        ]
                    );

                    if (null === $trimmedValue || '' === $trimmedValue) {
                        if (null !== $localizedText) {
                            $entity->remove($localizedText);
                        }
                        continue;
                    }

                    if (null === $localizedText) {
                        $localizedText = (new LocalizedText())
                            ->setTargetType(LocalizedText::TARGET_INTERPRETATION)
                            ->setTargetId((int) $interpretation->getId())
                            ->setField($fieldName)
                            ->setLocale('en');
                        $entity->persist($localizedText);
                    }

                    $localizedText->setValue($trimmedValue);
                    $localizedText->setIsAiGenerated(
                        $this->resolveAiGeneratedFlag(
                            $interpretationAiFlags,
                            $formFieldName,
                            $localizedText->isAiGenerated()
                        )
                    );
                }
            }
        }

        $entity->flush();
    }

    private function getSubmittedTranslationFieldName(string $targetType, string $fieldName): string
    {
        return str_replace(['__', '.'], ['____', '__'], $this->getTranslationFieldName($targetType, $fieldName));
    }

    private function extractAiFlagsFromRequest(): array
    {
        $request = $this->getRequest();
        if (null === $request) {
            return [];
        }

        $allData = $request->request->all();
        if (!isset($allData['localized_ai_flags']) || !is_array($allData['localized_ai_flags'])) {
            return [];
        }

        return $allData['localized_ai_flags'];
    }

    private function resolveAiGeneratedFlag(array $flags, string $formFieldName, bool $fallback): bool
    {
        if (!array_key_exists($formFieldName, $flags)) {
            return $fallback;
        }

        return in_array((string) $flags[$formFieldName], ['1', 'true', 'on'], true);
    }
}
