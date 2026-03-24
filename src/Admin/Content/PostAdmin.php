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

namespace App\Admin\Content;

use App\Admin\AbstractEntityAdmin;
use App\Persistence\Entity\Epigraphy\LocalizedText;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class PostAdmin extends AbstractEntityAdmin
{
    /** @var array<string, ?LocalizedText> */
    private array $localizedTextEntityCache = [];

    private const LOCALIZED_FIELDS = [
        'title' => 'string',
        'body' => 'string',
    ];

    protected $baseRouteName = 'content_post';

    protected $baseRoutePattern = 'content/post';

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createListOptions('id'))
            ->add('title', null, $this->createListOptions('title'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with($this->getSectionLabel('common'))
                ->add('title', CKEditorType::class, $this->createFormOptions('title', ['autoload' => true]))
                ->add(
                    $this->getLocalizedTextFieldNameForTarget(LocalizedText::TARGET_POST, 'title'),
                    TextType::class,
                    $this->createPostTranslationFieldOptions('title')
                )
                ->add('body', CKEditorType::class, $this->createFormOptions('body', ['autoload' => true]))
                ->add(
                    $this->getLocalizedTextFieldNameForTarget(LocalizedText::TARGET_POST, 'body'),
                    CKEditorType::class,
                    $this->createPostTranslationFieldOptions('body', ['autoload' => true])
                )
            ->end()
        ;
    }

    public function postPersist($object): void
    {
        $this->storeLocalizedTextFieldsForTarget(
            LocalizedText::TARGET_POST,
            null === $object ? null : $object->getId(),
            self::LOCALIZED_FIELDS
        );
    }

    public function postUpdate($object): void
    {
        $this->storeLocalizedTextFieldsForTarget(
            LocalizedText::TARGET_POST,
            null === $object ? null : $object->getId(),
            self::LOCALIZED_FIELDS
        );
    }

    private function createPostTranslationFieldOptions(string $fieldName, array $options = []): array
    {
        $targetId = null === $this->getSubject() ? null : $this->getSubject()->getId();

        return $this->createLocalizedTextOptionsForTarget(
            LocalizedText::TARGET_POST,
            $targetId,
            $fieldName,
            array_merge(
                [
                    'attr' => [
                        'data-auto-translate-ai-generated' => $this->isLocalizedTextAiGenerated($fieldName) ? '1' : '0',
                        'data-auto-translate-target-type' => LocalizedText::TARGET_POST,
                        'data-auto-translate-target-id' => (string) ($targetId ?? ''),
                        'data-auto-translate-target-field' => $fieldName,
                        'data-auto-translate-target-locale' => 'en',
                        'data-auto-translate-source-suffix' => sprintf('[%s]', $fieldName),
                        'data-auto-translate-target-lang' => 'en',
                        'data-auto-translate-source-lang' => 'ru',
                        'data-auto-translate-ai-label' => 'Переведено ИИ (черновик)',
                    ],
                ],
                $options
            )
        );
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
        $targetId = null === $this->getSubject() ? null : $this->getSubject()->getId();
        if (null === $targetId) {
            return null;
        }

        $cacheKey = sprintf('%d|%s', (int) $targetId, $fieldName);
        if (array_key_exists($cacheKey, $this->localizedTextEntityCache)) {
            return $this->localizedTextEntityCache[$cacheKey];
        }

        $localizedText = $this->getLocalizedTextEntityManager()->getRepository(LocalizedText::class)->findOneBy(
            [
                'targetType' => LocalizedText::TARGET_POST,
                'targetId' => (int) $targetId,
                'field' => $fieldName,
                'locale' => 'en',
            ]
        );
        $this->localizedTextEntityCache[$cacheKey] = $localizedText;

        return $localizedText;
    }

    private function getLocalizedTextEntityManager(): EntityManagerInterface
    {
        return $this->getModelManager()->getEntityManager(LocalizedText::class);
    }
}
