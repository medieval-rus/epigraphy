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

namespace App\Form;

use App\Persistence\Entity\Epigraphy\Inscription;
use App\Persistence\Entity\Epigraphy\ZeroRow;
use App\Persistence\Repository\Epigraphy\InterpretationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ZeroRowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groupOptions = [
            'inherit_data' => true,
            'data_class' => ZeroRow::class,
            'label_attr' => [
                'class' => 'eomr-embedded-form-group-label',
            ],
            'attr' => [
                'class' => 'eomr-embedded-form-group-content',
            ],
        ];

        $parentInscription = $options['parent_data'];

        if (!$parentInscription instanceof Inscription) {
            throw new \RuntimeException('Zero row cannot be created without having parent inscription.');
        }

        $referencesOptions = [
            'query_builder' => function (InterpretationRepository $entityRepository) use ($parentInscription) {
                return $entityRepository
                    ->createQueryBuilder('interpretation')
                    ->where('interpretation.inscription = :inscriptionId')
                    ->setParameter(':inscriptionId', $parentInscription->getId());
            },
        ];

        $builder
            ->add(
                $builder
                    ->create(
                        'group-materialAspect',
                        FormType::class,
                        array_merge($groupOptions, ['label' => 'form.inscription.group.materialAspect'])
                    )
                    ->add('placeOnCarrier')
                    ->add('placeOnCarrierReferences', null, $referencesOptions)
                    ->add('writingType')
                    ->add('writingTypeReferences', null, $referencesOptions)
                    ->add('writingMethod')
                    ->add('writingMethodReferences', null, $referencesOptions)
                    ->add('preservationState')
                    ->add('preservationStateReferences', null, $referencesOptions)
                    ->add('materials')
                    ->add('materialsReferences', null, $referencesOptions)
            )
            ->add(
                $builder
                    ->create(
                        'group-linguisticAspect',
                        FormType::class,
                        array_merge($groupOptions, ['label' => 'form.inscription.group.linguisticAspect'])
                    )
                    ->add('alphabet')
                    ->add('alphabetReferences', null, $referencesOptions)
                    ->add('text')
                    ->add('textReferences', null, $referencesOptions)
                    ->add('textImageFileNames')
                    ->add('textImageFileNamesReferences', null, $referencesOptions)
                    ->add('transliteration')
                    ->add('transliterationReferences', null, $referencesOptions)
                    ->add('translation')
                    ->add('translationReferences', null, $referencesOptions)
                    ->add('contentCategory')
                    ->add('contentCategoryReferences', null, $referencesOptions)
                    ->add('content')
                    ->add('contentReferences', null, $referencesOptions)
            )
            ->add(
                $builder
                    ->create(
                        'group-historicalAspect',
                        FormType::class,
                        array_merge($groupOptions, ['label' => 'form.inscription.group.historicalAspect'])
                    )
                    ->add('dateInText')
                    ->add('dateInTextReferences', null, $referencesOptions)
                    ->add('stratigraphicalDate')
                    ->add('stratigraphicalDateReferences', null, $referencesOptions)
                    ->add('nonStratigraphicalDate')
                    ->add('nonStratigraphicalDateReferences', null, $referencesOptions)
                    ->add('historicalDate')
                    ->add('historicalDateReferences', null, $referencesOptions)
            )
            ->add(
                $builder
                    ->create(
                        'group-multimedia',
                        FormType::class,
                        array_merge($groupOptions, ['label' => 'form.inscription.group.multimedia'])
                    )
                    ->add('photoFileNames')
                    ->add('photoFileNamesReferences', null, $referencesOptions)
                    ->add('sketchFileNames')
                    ->add('sketchFileNamesReferences', null, $referencesOptions)
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ZeroRow::class,
            'attr' => [
                'class' => 'eomr-embedded-form',
            ],
            'label_attr' => [
                'class' => 'eomr-embedded-form-label',
            ],
            'parent_data' => null,
        ]);
    }
}
