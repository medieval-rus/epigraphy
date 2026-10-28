<?php

declare(strict_types=1);

/*
 * This file is part of VyfonyFilterableTableBundle project.
 *
 * (c) Anton Dyshkant <vyshkant@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vyfony\Bundle\FilterableTableBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BooleanInclusiveChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'multiple' => true,
            'expanded' => true,
            'choice_value_false_label' => null,
            'choice_value_true_label' => null,
        ]);

        $resolver->setAllowedTypes('choice_value_false_label', ['string']);
        $resolver->setAllowedTypes('choice_value_true_label', ['string']);

        $resolver->setNormalizer('choices', function (Options $options): array {
            return [
                $options['choice_value_false_label'] => false,
                $options['choice_value_true_label'] => true,
            ];
        });
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'boolean_inclusive';
    }
}
