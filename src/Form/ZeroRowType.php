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

use App\Persistence\Entity\Epigraphy\Inscription\ZeroRow;
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

        $builder
            ->add(
                $builder
                    ->create(
                        'group-materialAspect',
                        FormType::class,
                        array_merge($groupOptions, ['label' => 'form.inscription.group.materialAspect'])
                    )
                    ->add('placeOnCarrier')
                    ->add('placeOnCarrierReferences')
                    ->add('writingType')
                    ->add('writingTypeReferences')
                    ->add('writingMethod')
                    ->add('writingMethodReferences')
                    ->add('preservationState')
                    ->add('preservationStateReferences')
                    ->add('materials')
                    ->add('materialsReferences')
            )
            ->add(
                $builder
                    ->create(
                        'group-linguisticAspect',
                        FormType::class,
                        array_merge($groupOptions, ['label' => 'form.inscription.group.linguisticAspect'])
                    )
                    ->add('alphabet')
                    ->add('alphabetReferences')
                    ->add('text')
                    ->add('textReferences')
                    ->add('textImageFileNames')
                    ->add('textImageFileNamesReferences')
                    ->add('transliteration')
                    ->add('transliterationReferences')
                    ->add('translation')
                    ->add('translationReferences')
                    ->add('contentCategory')
                    ->add('contentCategoryReferences')
                    ->add('content')
                    ->add('contentReferences')
            )
            ->add(
                $builder
                    ->create(
                        'group-historicalAspect',
                        FormType::class,
                        array_merge($groupOptions, ['label' => 'form.inscription.group.historicalAspect'])
                    )
                    ->add('dateInText')
                    ->add('dateInTextReferences')
                    ->add('stratigraphicalDate')
                    ->add('stratigraphicalDateReferences')
                    ->add('nonStratigraphicalDate')
                    ->add('nonStratigraphicalDateReferences')
                    ->add('historicalDate')
                    ->add('historicalDateReferences')
            )
            ->add(
                $builder
                    ->create(
                        'group-multimedia',
                        FormType::class,
                        array_merge($groupOptions, ['label' => 'form.inscription.group.multimedia'])
                    )
                    ->add('photoFileNames')
                    ->add('photoFileNamesReferences')
                    ->add('sketchFileNames')
                    ->add('sketchFileNamesReferences')
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
        ]);
    }
}
