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

namespace Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\RadioOption\RadioOptionInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadataInterface;

final class RadioColumnChoiceTableParameter extends AbstractTableParameter
{
    /**
     * @var RadioOptionInterface[]
     */
    private $radioOptions = [];

    /**
     * @return RadioColumnChoiceTableParameter
     */
    public function addRadioOption(RadioOptionInterface $radioOption): self
    {
        $this->radioOptions[$radioOption->getName()] = $radioOption;

        return $this;
    }

    public function getType(): string
    {
        return ChoiceType::class;
    }

    /**
     * @return ColumnMetadataInterface[]
     */
    public function getColumnMetadataCollection(array $queryParameters): array
    {
        return [$this->radioOptions[$queryParameters[$this->getQueryParameterName()]]->getColumnMetadata()];
    }

    public function getDefaultValue(): string
    {
        reset($this->radioOptions);

        return key($this->radioOptions);
    }

    protected function createOptions(EntityManager $entityManager): array
    {
        $choices = [];
        foreach ($this->radioOptions as $radioOption) {
            $choices[$radioOption->getLabel()] = $radioOption->getName();
        }

        return array_merge(parent::createOptions($entityManager), [
            'multiple' => false,
            'expanded' => true,
            'choices' => $choices,
        ]);
    }
}
