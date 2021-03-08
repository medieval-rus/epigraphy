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
 * in the hope  that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\FilterableTable;

use App\FilterableTable\Filter\Parameter\AlphabetFilterParameter;
use App\FilterableTable\Filter\Parameter\CarrierCategoryFilterParameter;
use App\FilterableTable\Filter\Parameter\CarrierTypeFilterParameter;
use App\FilterableTable\Filter\Parameter\PreservationStateFilterParameter;
use App\FilterableTable\Filter\Parameter\WritingMethodFilterParameter;
use App\FilterableTable\Filter\Parameter\WritingTypeFilterParameter;
use App\Persistence\Entity\Epigraphy\Inscription;
use App\Services\ActualValue\Formatter\ActualValueFormatterInterface;
use App\Services\Value\ValueStringifierInterface;
use InvalidArgumentException;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\AbstractFilterConfigurator;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\RadioColumnChoiceTableParameter;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\RadioOption\RadioOption;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\TableParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Restriction\FilterRestrictionInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadata;

final class InscriptionsFilterConfigurator extends AbstractFilterConfigurator
{
    /**
     * @var ValueStringifierInterface
     */
    private $valueStringifier;

    /**
     * @var CarrierTypeFilterParameter
     */
    private $carrierTypeFilterParameter;

    /**
     * @var CarrierCategoryFilterParameter
     */
    private $carrierCategoryFilterParameter;

    /**
     * @var WritingTypeFilterParameter
     */
    private $writingTypeFilterParameter;

    /**
     * @var WritingMethodFilterParameter
     */
    private $writingMethodFilterParameter;

    /**
     * @var PreservationStateFilterParameter
     */
    private $preservationStateFilterParameter;

    /**
     * @var AlphabetFilterParameter
     */
    private $alphabetFilterParameter;

    public function __construct(
        ValueStringifierInterface $valueStringifier,
        CarrierTypeFilterParameter $carrierTypeFilterParameter,
        CarrierCategoryFilterParameter $carrierCategoryFilterParameter,
        WritingTypeFilterParameter $writingTypeFilterParameter,
        WritingMethodFilterParameter $writingMethodFilterParameter,
        PreservationStateFilterParameter $preservationStateFilterParameter,
        AlphabetFilterParameter $alphabetFilterParameter
    ) {
        $this->valueStringifier = $valueStringifier;
        $this->carrierTypeFilterParameter = $carrierTypeFilterParameter;
        $this->carrierCategoryFilterParameter = $carrierCategoryFilterParameter;
        $this->writingTypeFilterParameter = $writingTypeFilterParameter;
        $this->writingMethodFilterParameter = $writingMethodFilterParameter;
        $this->preservationStateFilterParameter = $preservationStateFilterParameter;
        $this->alphabetFilterParameter = $alphabetFilterParameter;
    }

    public function createSubmitButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-primary'],
            'label' => 'controller.inscription.list.filter.submitButton',
        ];
    }

    public function createResetButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-secondary'],
            'label' => 'controller.inscription.list.filter.resetButton',
        ];
    }

    public function createSearchInFoundButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-warning'],
            'label' => 'controller.inscription.list.filter.searchInFoundButton',
        ];
    }

    public function createDefaults(): array
    {
        return [
            'label_attr' => ['class' => ''],
            'translation_domain' => 'messages',
            'attr' => ['class' => 'row'],
            'method' => 'GET',
            'csrf_protection' => false,
            'required' => false,
        ];
    }

    public function getDisablePaginationLabel(): string
    {
        return 'controller.inscription.list.filter.disablePaginator';
    }

    /**
     * @param mixed $entity
     *
     * @return mixed
     */
    public function getEntityId($entity)
    {
        if (!$entity instanceof Inscription) {
            $message = sprintf('Expected entity of type "%s", "%s" given', Inscription::class, $entity);

            throw new InvalidArgumentException($message);
        }

        return $entity->getId();
    }

    /**
     * @return FilterRestrictionInterface[]
     */
    protected function createFilterRestrictions(): array
    {
        return [];
    }

    /**
     * @return FilterParameterInterface[]
     */
    protected function createFilterParameters(): array
    {
        return [
            $this->carrierTypeFilterParameter,
            $this->carrierCategoryFilterParameter,
            $this->writingTypeFilterParameter,
            $this->writingMethodFilterParameter,
            $this->preservationStateFilterParameter,
            $this->alphabetFilterParameter,
        ];
    }

    /**
     * @return TableParameterInterface[]
     */
    protected function createTableParameters(): array
    {
        return [
            (new RadioColumnChoiceTableParameter())
                ->addRadioOption(
                    (new RadioOption())
                        ->setName('content')
                        ->setLabel('controller.inscription.list.filter.dataColumn.option.content')
                        ->setColumnMetadata(
                            (new ColumnMetadata())
                                ->setIsRaw(true)
                                ->setName('interpretation-content')
                                ->setValueExtractor(function (Inscription $inscription): string {
                                    return $this->valueStringifier->stringify($inscription, 'content') ?? '-';
                                })
                                ->setLabel('controller.inscription.list.table.column.interpretation.content')
                        )
                )
                ->addRadioOption(
                    (new RadioOption())
                        ->setName('text')
                        ->setLabel('controller.inscription.list.filter.dataColumn.option.text')
                        ->setColumnMetadata(
                            (new ColumnMetadata())
                                ->setIsRaw(true)
                                ->setName('interpretation-text')
                                ->setValueExtractor(function (Inscription $inscription): string {
                                    return $this->valueStringifier->stringify(
                                        $inscription,
                                        'text',
                                        ActualValueFormatterInterface::FORMAT_TYPE_ORIGINAL_TEXT
                                    ) ?? '-';
                                })
                                ->setLabel('controller.inscription.list.table.column.interpretation.text')
                        )
                )
                ->setQueryParameterName('dataColumn')
                ->setLabel('controller.inscription.list.filter.dataColumn.label'),
        ];
    }
}
