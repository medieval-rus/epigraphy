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

namespace App\Admin;

use App\Persistence\Entity\Epigraphy\LocalizedText;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Doctrine\ORM\EntityRepository;

abstract class AbstractEntityAdmin extends AbstractAdmin
{
    protected function configure(): void
    {
        $entityKey = $this->getEntityKey();
        $em = $this->getModelManager()->getEntityManager('App\Persistence\Entity\Bibliography\BibliographicRecord');
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('b.shortName')->from('App\Persistence\Entity\Bibliography\BibliographicRecord', 'b');
        $this->bibliography = json_encode($queryBuilder->getQuery()->execute());

        $this->classnameLabel = $entityKey;
        $this->setLabel('menu.paragraphs.'.$entityKey.'.label');
        $this->setTranslationDomain('admin');
    }

    protected function getEntityKey(): string
    {
        return lcfirst((new ReflectionClass($this->getClass()))->getShortName());
    }

    protected function getFormKeyForFieldName(string $fieldName): string
    {
        return 'form.'.$this->getEntityKey().'.fields.'.$fieldName;
    }

    protected function getListKeyForFieldName(string $fieldName): string
    {
        return 'list.'.$this->getEntityKey().'.fields.'.$fieldName;
    }

    protected function createListOptions(string $fieldName, array $options = []): array
    {
        return array_merge(
            [
                'label' => $this->getListKeyForFieldName($fieldName),
            ],
            $options
        );
    }

    protected function createFormOptions(string $fieldName, array $options = []): array
    {
        return array_merge(
            [
                'label' => $this->getFormKeyForFieldName($fieldName),
            ],
            $options
        );
    }

    protected function createManyToManyFormOptions(string $fieldName, array $options = []): array
    {
        return $this->createFormOptions(
            $fieldName,
            array_merge(
                ['required' => false, 'multiple' => true],
                $options
            )
        );
    }

    protected function createEntityOptions(string $fieldName, string $class) {
        return $this->createFormOptions(
            $fieldName,
            array_merge([
                'class' => $class,
                'required' => false,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c');
                }
            ])
        );
    }

    protected function createFilteredEntityOptions(string $fieldName, string $class, string $param, string $value = 'true'): array
    {
        return $this->createFormOptions(
            $fieldName,
            array_merge([
                'class' => $class,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($param, $value) {
                    return $er->createQueryBuilder('c')->where('c.'.$param.' = '.$value);
                }
            ])
        );
    }

    protected function getSectionLabel(string $key): string
    {
        return 'form.'.$this->getEntityKey().'.section.'.$key.'.label';
    }

    protected function getTabLabel(string $key): string
    {
        return 'form.'.$this->getEntityKey().'.tab.'.$key.'.label';
    }

    protected function getLocalizedTextFieldNameForTarget(string $targetType, string $fieldName): string
    {
        return 'localizedEn__'.$targetType.'__'.$fieldName;
    }

    protected function getSubmittedLocalizedTextFieldNameForTarget(string $targetType, string $fieldName): string
    {
        return str_replace(['__', '.'], ['____', '__'], $this->getLocalizedTextFieldNameForTarget($targetType, $fieldName));
    }

    protected function createLocalizedTextOptionsForTarget(
        string $targetType,
        ?int $targetId,
        string $fieldName,
        array $options = []
    ): array {
        return array_merge(
            [
                'mapped' => false,
                'required' => false,
                'label' => sprintf('%s (EN)', $fieldName),
                'data' => $this->getLocalizedTextValueForTarget($targetType, $targetId, $fieldName),
            ],
            $options
        );
    }

    protected function getLocalizedTextValueForTarget(string $targetType, ?int $targetId, string $fieldName): ?string
    {
        if (null === $targetId) {
            return null;
        }

        $entityManager = $this->getLocalizedTextEntityManager();
        $localizedText = $entityManager->getRepository(LocalizedText::class)->findOneBy(
            [
                'targetType' => $targetType,
                'targetId' => $targetId,
                'field' => $fieldName,
                'locale' => 'en',
            ]
        );

        return null === $localizedText ? null : $localizedText->getValue();
    }

    protected function getLocalizedTextArrayValueForTarget(string $targetType, ?int $targetId, string $fieldName): array
    {
        $value = $this->getLocalizedTextValueForTarget($targetType, $targetId, $fieldName);
        if (null === $value || '' === trim($value)) {
            return [];
        }

        $decodedValue = json_decode($value, true);
        if (!is_array($decodedValue)) {
            return [];
        }

        return array_values(
            array_filter(
                array_map(
                    static function ($item): ?string {
                        if (!is_string($item)) {
                            return null;
                        }

                        $trimmedItem = trim($item);

                        return '' === $trimmedItem ? null : $trimmedItem;
                    },
                    $decodedValue
                ),
                static function (?string $item): bool {
                    return null !== $item;
                }
            )
        );
    }

    protected function storeLocalizedTextFieldsForTarget(string $targetType, ?int $targetId, array $fieldTypes): void
    {
        if (null === $targetId) {
            return;
        }

        $entityManager = $this->getLocalizedTextEntityManager();
        $repository = $entityManager->getRepository(LocalizedText::class);
        $form = $this->getForm();

        foreach ($fieldTypes as $fieldName => $fieldType) {
            $formFieldName = $this->getSubmittedLocalizedTextFieldNameForTarget($targetType, $fieldName);
            if (!$form->has($formFieldName)) {
                continue;
            }

            $storedValue = $this->normalizeLocalizedTextStoredValue(
                $form->get($formFieldName)->getData(),
                $fieldType
            );

            $localizedText = $repository->findOneBy(
                [
                    'targetType' => $targetType,
                    'targetId' => $targetId,
                    'field' => $fieldName,
                    'locale' => 'en',
                ]
            );

            if (null === $storedValue) {
                if (null !== $localizedText) {
                    $entityManager->remove($localizedText);
                }
                continue;
            }

            if (null === $localizedText) {
                $localizedText = (new LocalizedText())
                    ->setTargetType($targetType)
                    ->setTargetId($targetId)
                    ->setField($fieldName)
                    ->setLocale('en');
                $entityManager->persist($localizedText);
            }

            $localizedText->setValue($storedValue);
        }

        $entityManager->flush();
    }

    private function normalizeLocalizedTextStoredValue($value, string $fieldType): ?string
    {
        if ('array' === $fieldType) {
            if (!is_array($value)) {
                return null;
            }

            $normalizedItems = array_values(
                array_filter(
                    array_map(
                        static function ($item): ?string {
                            if (!is_string($item)) {
                                return null;
                            }

                            $trimmedItem = trim($item);

                            return '' === $trimmedItem ? null : $trimmedItem;
                        },
                        $value
                    ),
                    static function (?string $item): bool {
                        return null !== $item;
                    }
                )
            );

            if ([] === $normalizedItems) {
                return null;
            }

            return json_encode($normalizedItems, JSON_UNESCAPED_UNICODE);
        }

        if (null === $value) {
            return null;
        }

        $trimmedValue = trim((string) $value);

        return '' === $trimmedValue ? null : $trimmedValue;
    }

    private function getLocalizedTextEntityManager(): EntityManagerInterface
    {
        return $this->getModelManager()->getEntityManager(LocalizedText::class);
    }
}
