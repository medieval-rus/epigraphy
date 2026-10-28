<?php

declare(strict_types=1);

namespace App\FilterableTable\Filter\Parameter;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;

/** The initial-year filter applies this option when matching the date range. */
final class ConventionalDateExactMatchFilterParameter implements FilterParameterInterface
{
    public function getQueryParameterName(): string
    {
        return 'conventionalDateExactMatch';
    }

    public function getType(): string
    {
        return CheckboxType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        return [
            'required' => false,
            'false_values' => [null, '', '0'],
            'label' => 'controller.inscription.list.filter.dateSlider.exactMatch',
        ];
    }
}
