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
use App\Persistence\Entity\Epigraphy\Carrier;
use App\Persistence\Entity\Epigraphy\LocalizedText;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

final class CarrierAdmin extends AbstractEntityAdmin
{
    private const TRANSLATABLE_FIELDS = [
        'origin1' => TextType::class,
        'origin2' => TextType::class,
        'findCircumstances' => CKEditorType::class,
        'carrierHistory' => CKEditorType::class,
        'archaeology' => CKEditorType::class,
        'characteristics' => CKEditorType::class,
        'individualName' => TextType::class,
        'storagePlace' => TextType::class,
        'inventoryNumber' => TextType::class,
        'stratigraphicalDate' => CKEditorType::class,
        'previousStorage' => CKEditorType::class,
        'storageLocalization' => CKEditorType::class,
        'materialDescription' => CKEditorType::class,
    ];

    protected $baseRouteName = 'epigraphy_carrier';

    protected $baseRoutePattern = 'epigraphy/carrier';

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createListOptions('id'))
            ->add('discoverySite', null, $this->createListOptions('discoverySite'))
            ->add('individualName', null, $this->createListOptions('individualName'))
            ->add('supercarrier', null, $this->createListOptions('supercarrier'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('individualName', null, $this->createFormOptions('individualName'))
            ->add('categories', null, $this->createManyToManyFormOptions('categories'))
            ->add(
                'supercarrier',
                EntityType::class,
                $this->createFilteredEntityOptions('supercarrier', Carrier::class, 'isSuperCarrier') 
            )
            ->add(
                'isSuperCarrier',
                CheckboxType::class,
                $this->createFormOptions('isSuperCarrier', ['required' => false])
            )
            ->add('characteristics', CKEditorType::class, $this->createFormOptions('characteristics', ['autoload' => false]))
            ->add('materials', null, $this->createManyToManyFormOptions('materials'))
            ->add('materialDescription', CKEditorType::class, $this->createFormOptions('materialDescription', ['autoload' => false, 'required' => false]))
            ->add('stratigraphicalDate', CKEditorType::class, $this->createFormOptions('stratigraphicalDate', ['autoload' => false, 'required' => false]))
            ->add('findCircumstances', CKEditorType::class, $this->createFormOptions('findCircumstances', ['autoload' => false, 'required' => false]))
            // ->add('carrierHistory', CKEditorType::class, $this->createFormOptions('carrierHistory', ['required' => false, 'autoload' => false]))
            ->add('discoverySite', null, $this->createManyToManyFormOptions('discoverySite'))
            // археология
            ->add('archaeology', CKEditorType::class, $this->createFormOptions('archaeology', ['required' => false, 'autoload' => false]))
            // предыдущие места хранения
            ->add('previousStorage', CKEditorType::class, $this->createFormOptions('previousStorage', ['required' => false, 'autoload' => false]))
            ->add('storageSite', null, $this->createManyToManyFormOptions('storageSite'))
            // локализация в месте хранения
            ->add('storageLocalization', CKEditorType::class, $this->createFormOptions('storageLocalization', ['required' => false, 'autoload' => false]))
            ->add('inventoryNumber', null, $this->createFormOptions('inventoryNumber'))
        ;

        foreach (self::TRANSLATABLE_FIELDS as $fieldName => $fieldType) {
            $options = [
                'mapped' => false,
                'required' => false,
                'label' => sprintf('%s (EN)', $fieldName),
                'data' => $this->getLocalizedTextValue($fieldName),
            ];
            if (CKEditorType::class === $fieldType) {
                $options['autoload'] = false;
            }

            $formMapper->add(
                $this->getTranslationFieldName($fieldName),
                $fieldType,
                $options
            );
        }
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
        return 'localizedEn__'.$fieldName;
    }

    private function getLocalizedTextValue(string $fieldName): ?string
    {
        $subject = $this->getSubject();
        if (null === $subject || null === $subject->getId()) {
            return null;
        }

        $entity = $this->getModelManager()->getEntityManager(LocalizedText::class);
        $localizedText = $entity->getRepository(LocalizedText::class)->findOneBy(
            [
                'targetType' => LocalizedText::TARGET_CARRIER,
                'targetId' => (int) $subject->getId(),
                'field' => $fieldName,
                'locale' => 'en',
            ]
        );

        return null === $localizedText ? null : $localizedText->getValue();
    }

    private function storeLocalizedTexts(Carrier $carrier): void
    {
        if (null === $carrier->getId()) {
            return;
        }

        $entity = $this->getModelManager()->getEntityManager(LocalizedText::class);
        $repository = $entity->getRepository(LocalizedText::class);
        $form = $this->getForm();

        foreach (array_keys(self::TRANSLATABLE_FIELDS) as $fieldName) {
            $formFieldName = $this->getSubmittedTranslationFieldName($fieldName);
            if (!$form->has($formFieldName)) {
                continue;
            }

            $value = $form->get($formFieldName)->getData();
            $trimmedValue = null === $value ? null : trim((string) $value);

            $localizedText = $repository->findOneBy(
                [
                    'targetType' => LocalizedText::TARGET_CARRIER,
                    'targetId' => (int) $carrier->getId(),
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
                    ->setTargetType(LocalizedText::TARGET_CARRIER)
                    ->setTargetId((int) $carrier->getId())
                    ->setField($fieldName)
                    ->setLocale('en');
                $entity->persist($localizedText);
            }

            $localizedText->setValue($trimmedValue);
        }

        $entity->flush();
    }

    private function getSubmittedTranslationFieldName(string $fieldName): string
    {
        return str_replace(['__', '.'], ['____', '__'], $this->getTranslationFieldName($fieldName));
    }
}
