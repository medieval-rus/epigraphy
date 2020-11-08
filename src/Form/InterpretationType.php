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

use App\Persistence\Entity\Epigraphy\Interpretation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class InterpretationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groupOptions = [
            'inherit_data' => true,
            'data_class' => Interpretation::class,
            'label_attr' => [
                'class' => 'eomr-embedded-form-group-label',
            ],
            'attr' => [
                'class' => 'eomr-embedded-form-group-content',
            ],
        ];

        $builder
            ->add('source')
            ->add('comment')
            ->add(
                $builder
                    ->create(
                        'group-bibliographicAspect',
                        FormType::class,
                        array_merge($groupOptions, ['label' => 'form.inscription.group.bibliographicAspect'])
                    )
                    ->add('pageNumbersInSource')
                    ->add('numberInSource')
            )
            ->add(
                $builder
                    ->create(
                        'group-materialAspect',
                        FormType::class,
                        array_merge($groupOptions, ['label' => 'form.inscription.group.materialAspect'])
                    )
                    ->add('placeOnCarrier')
                    ->add('writingType')
                    ->add('writingMethod')
                    ->add('preservationState')
                    ->add('materials')
            )
            ->add(
                $builder
                    ->create(
                        'group-linguisticAspect',
                        FormType::class,
                        array_merge($groupOptions, ['label' => 'form.inscription.group.linguisticAspect'])
                    )
                    ->add('alphabet')
                    ->add('text')
                    ->add('textImageFileNames')
                    ->add('transliteration')
                    ->add('translation')
                    ->add('contentCategory')
                    ->add('content')
            )
            ->add(
                $builder
                    ->create(
                        'group-historicalAspect',
                        FormType::class,
                        array_merge($groupOptions, ['label' => 'form.inscription.group.historicalAspect'])
                    )
                    ->add('dateInText')
                    ->add('stratigraphicalDate')
                    ->add('nonStratigraphicalDate')
                    ->add('historicalDate')
            )
            ->add(
                $builder
                    ->create(
                        'group-multimedia',
                        FormType::class,
                        array_merge($groupOptions, ['label' => 'form.inscription.group.multimedia'])
                    )
                    ->add('photoFileNames')
                    ->add('sketchFileNames')
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Interpretation::class,
            'attr' => [
                'class' => 'eomr-embedded-form',
            ],
            'label_attr' => [
                'class' => 'eomr-embedded-form-label',
            ],
        ]);
    }
}
