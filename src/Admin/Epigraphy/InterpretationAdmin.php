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
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

final class InterpretationAdmin extends AbstractEntityAdmin
{
    /** @var array<string, ?LocalizedText> */
    private array $localizedTextEntityCache = [];

    private const TRANSLATABLE_FIELDS = [
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

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with($this->getSectionLabel('identification'), ['class' => 'col-md-6'])
                ->add(
                    'id',
                    HiddenType::class,
                    ['attr' => ['data-interpretation-id' => $this->getSubject()->getId()]]
                )
                ->add('source', null, $this->createFormOptions('source'))
                ->add('pageNumbersInSource', null, $this->createFormOptions('pageNumbersInSource'))
                ->add('numberInSource', null, $this->createFormOptions('numberInSource'))
                ->add('comment', CKEditorType::class, $this->createFormOptions('comment', ['autoload' => false, 'required' => false]))
                ->add(
                    $this->getTranslationFieldName('comment'),
                    CKEditorType::class,
                    $this->createTranslationFieldOptions('comment')
                )
            ->end()
            ->with($this->getSectionLabel('materialAspect'), ['class' => 'col-md-6'])
                ->add('placeOnCarrier', CKEditorType::class, $this->createZeroRowPartOptions('placeOnCarrier', ['autoload' => false]))
                ->add(
                    $this->getTranslationFieldName('placeOnCarrier'),
                    CKEditorType::class,
                    $this->createTranslationFieldOptions('placeOnCarrier')
                )
                // ->add('writingTypes', null, $this->createZeroRowPartOptions('writingTypes'))
                ->add('writingMethods', null, $this->createZeroRowPartOptions('writingMethods'))
                ->add('preservationStates', null, $this->createZeroRowPartOptions('preservationStates'))
                // ->add('materials', null, $this->createZeroRowPartOptions('materials'))
            ->end()
            ->with($this->getSectionLabel('linguisticAspect'), ['class' => 'col-md-6'])
                ->add('alphabets', null, $this->createZeroRowPartOptions('alphabets'))
                ->add('text', CKEditorType::class, $this->createZeroRowPartOptions('text', ['autoload' => false, 'config_name' => 'textconfig']))
                ->add(
                    $this->getTranslationFieldName('text'),
                    CKEditorType::class,
                    $this->createTranslationFieldOptions('text')
                )
                ->add('interpretationComment', CKEditorType::class, $this->createZeroRowPartOptions('interpretationComment', ['autoload' => false, 'required' => false]))
                ->add(
                    $this->getTranslationFieldName('interpretationComment'),
                    CKEditorType::class,
                    $this->createTranslationFieldOptions('interpretationComment')
                )
                ->add('transliteration', CKEditorType::class, $this->createZeroRowPartOptions('transliteration', ['autoload' => false, 'config_name' => 'textconfig']))
                ->add(
                    $this->getTranslationFieldName('transliteration'),
                    CKEditorType::class,
                    $this->createTranslationFieldOptions('transliteration')
                )
                ->add('reconstruction', CKEditorType::class, $this->createZeroRowPartOptions('reconstruction', ['autoload' => false, 'config_name' => 'textconfig']))
                ->add(
                    $this->getTranslationFieldName('reconstruction'),
                    CKEditorType::class,
                    $this->createTranslationFieldOptions('reconstruction')
                )
                ->add('normalization', CKEditorType::class, $this->createZeroRowPartOptions('normalization', ['autoload' => false, 'config_name' => 'textconfig']))
                ->add(
                    $this->getTranslationFieldName('normalization'),
                    CKEditorType::class,
                    $this->createTranslationFieldOptions('normalization')
                )
                ->add('translation', CKEditorType::class, $this->createZeroRowPartOptions('translation', ['autoload' => false]))
                ->add(
                    $this->getTranslationFieldName('translation'),
                    CKEditorType::class,
                    $this->createTranslationFieldOptions('translation')
                )
                ->add('contentCategories', null, $this->createZeroRowPartOptions('contentCategories'))
                ->add('description', CKEditorType::class, $this->createZeroRowPartOptions('description', ['autoload' => false, 'config_name' => 'textconfig']))
                ->add(
                    $this->getTranslationFieldName('description'),
                    CKEditorType::class,
                    $this->createTranslationFieldOptions('description')
                )
            ->end()
            ->with($this->getSectionLabel('historicalAspect'), ['class' => 'col-md-6'])
                ->add('dateInText', CKEditorType::class, $this->createZeroRowPartOptions('dateInText', ['autoload' => false]))
                ->add(
                    $this->getTranslationFieldName('dateInText'),
                    CKEditorType::class,
                    $this->createTranslationFieldOptions('dateInText')
                )
                ->add('nonStratigraphicalDate', CKEditorType::class, $this->createZeroRowPartOptions('nonStratigraphicalDate', ['autoload' => false]))
                ->add(
                    $this->getTranslationFieldName('nonStratigraphicalDate'),
                    CKEditorType::class,
                    $this->createTranslationFieldOptions('nonStratigraphicalDate')
                )
                ->add('historicalDate', CKEditorType::class, $this->createZeroRowPartOptions('historicalDate', ['autoload' => false]))
                ->add(
                    $this->getTranslationFieldName('historicalDate'),
                    CKEditorType::class,
                    $this->createTranslationFieldOptions('historicalDate')
                )
                ->add('origin', CKEditorType::class, $this->createZeroRowPartOptions('origin', ['autoload' => false]))
                ->add(
                    $this->getTranslationFieldName('origin'),
                    CKEditorType::class,
                    $this->createTranslationFieldOptions('origin')
                )
            ->end()
            ->with($this->getSectionLabel('media'), ['class' => 'col-md-6'])
                ->add(
                    'photos',
                    null,
                    $this->createZeroRowPartOptions(
                        'photos',
                        [
                            'choice_filter' => $this->dataStorageManager->getFolderFilter('photo'),
                            'query_builder' => $this->dataStorageManager->getQueryBuilder(),
                        ]
                    )
                )
                ->add(
                    'drawings',
                    null,
                    $this->createZeroRowPartOptions(
                        'drawings',
                        [
                            'choice_filter' => $this->dataStorageManager->getFolderFilter('drawing'),
                            'query_builder' => $this->dataStorageManager->getQueryBuilder(),
                        ]
                    )
                )
                ->add(
                    'textImages',
                    null,
                    $this->createZeroRowPartOptions(
                        'textImages',
                        [
                            'choice_filter' => $this->dataStorageManager->getFolderFilter('text'),
                            'query_builder' => $this->dataStorageManager->getQueryBuilder(),
                        ]
                    )
                )
            ->end()
        ;
    }

    private function createZeroRowPartOptions(
        string $label,
        array $options = []
    ): array {
        $options['attr'] = array_merge(
            [
                'data-zero-row-part' => $label.'References',
            ],
            \array_key_exists('attr', $options) ? $options['attr'] : []
        );

        return $this->createFormOptions($label, array_merge(['required' => false], $options));
    }

    public function postPersist($object): void
    {
        $this->storeLocalizedTexts($object);
    }

    public function postUpdate($object): void
    {
        $this->storeLocalizedTexts($object);
    }

    private function getTranslationFieldName(string $fieldName): string
    {
        return 'localizedEn__interpretation__'.$fieldName;
    }

    private function createTranslationFieldOptions(string $fieldName): array
    {
        $options = [
            'mapped' => false,
            'required' => false,
            'label' => sprintf('%s (EN)', $fieldName),
            'data' => $this->getLocalizedTextValue($fieldName),
            'autoload' => false,
            'attr' => [
                'data-auto-translate-ai-generated' => $this->isLocalizedTextAiGenerated($fieldName) ? '1' : '0',
                'data-auto-translate-target-type' => LocalizedText::TARGET_INTERPRETATION,
                'data-auto-translate-target-id' => (string) (($this->getSubject() && null !== $this->getSubject()->getId()) ? $this->getSubject()->getId() : ''),
                'data-auto-translate-target-field' => $fieldName,
                'data-auto-translate-target-locale' => 'en',
            ],
        ];

        if ('text' !== $fieldName) {
            $options['attr']['data-auto-translate-source-suffix'] = sprintf('[%s]', $fieldName);
            $options['attr']['data-auto-translate-target-lang'] = 'en';
            $options['attr']['data-auto-translate-source-lang'] = 'ru';
        }

        return $options;
    }

    private function getLocalizedTextValue(string $fieldName): ?string
    {
        $localizedText = $this->getLocalizedTextEntity($fieldName);

        return null === $localizedText ? null : $localizedText->getValue();
    }

    private function isLocalizedTextAiGenerated(string $fieldName): bool
    {
        $localizedText = $this->getLocalizedTextEntity($fieldName);

        if (null === $localizedText) {
            return false;
        }

        return $localizedText->isAiGenerated();
    }

    private function getLocalizedTextEntity(string $fieldName): ?LocalizedText
    {
        $subject = $this->getSubject();
        if (null === $subject || null === $subject->getId()) {
            return null;
        }

        $targetId = (int) $subject->getId();
        $cacheKey = sprintf('%s|%d|%s|en', LocalizedText::TARGET_INTERPRETATION, $targetId, $fieldName);
        if (array_key_exists($cacheKey, $this->localizedTextEntityCache)) {
            return $this->localizedTextEntityCache[$cacheKey];
        }

        $entity = $this->getModelManager()->getEntityManager(LocalizedText::class);
        $localizedText = $entity->getRepository(LocalizedText::class)->findOneBy(
            [
                'targetType' => LocalizedText::TARGET_INTERPRETATION,
                'targetId' => $targetId,
                'field' => $fieldName,
                'locale' => 'en',
            ]
        );
        $this->localizedTextEntityCache[$cacheKey] = $localizedText;

        return $localizedText;
    }

    private function storeLocalizedTexts(Interpretation $interpretation): void
    {
        if (null === $interpretation->getId()) {
            return;
        }

        $entity = $this->getModelManager()->getEntityManager(LocalizedText::class);
        $repository = $entity->getRepository(LocalizedText::class);
        $form = $this->getForm();
        $aiFlags = $this->extractAiFlagsFromRequest();

        foreach (array_keys(self::TRANSLATABLE_FIELDS) as $fieldName) {
            $formFieldName = $this->getSubmittedTranslationFieldName($fieldName);
            if (!$form->has($formFieldName)) {
                continue;
            }

            $value = $form->get($formFieldName)->getData();
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
                $this->resolveAiGeneratedFlag($aiFlags, $formFieldName, $localizedText->isAiGenerated())
            );
        }

        $entity->flush();
    }

    private function getSubmittedTranslationFieldName(string $fieldName): string
    {
        return str_replace(['__', '.'], ['____', '__'], $this->getTranslationFieldName($fieldName));
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
