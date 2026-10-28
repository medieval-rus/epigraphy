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

namespace Vyfony\Bundle\FilterableTableBundle\Filter\Configurator;

use InvalidArgumentException;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\TableParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Restriction\FilterRestrictionInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting\CustomSortConfigurationInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting\DbSortConfiguration;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting\DbSortConfigurationInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting\SortConfigurationInterface;

abstract class AbstractFilterConfigurator implements FilterConfiguratorInterface
{
    /**
     * @var FilterRestrictionInterface[]
     */
    private $filterRestrictions;

    /**
     * @var FilterParameterInterface[]
     */
    private $filterParameters;

    /**
     * @var TableParameterInterface[]
     */
    private $tableParameters;

    /**
     * @var DbSortConfiguration|null
     */
    private $dbSortConfiguration;

    /**
     * @var CustomSortConfigurationInterface|null
     */
    private $customSortConfiguration;

    /**
     * @return FilterRestrictionInterface[]
     */
    final public function getFilterRestrictions(): array
    {
        if (null === $this->filterRestrictions) {
            $this->filterRestrictions = $this->createFilterRestrictions();
        }

        return $this->filterRestrictions;
    }

    /**
     * @return FilterParameterInterface[]
     */
    final public function getFilterParameters(): array
    {
        if (null === $this->filterParameters) {
            $this->filterParameters = $this->createFilterParameters();
        }

        return $this->filterParameters;
    }

    /**
     * @return TableParameterInterface[]
     */
    final public function getTableParameters(): array
    {
        if (null === $this->tableParameters) {
            $this->tableParameters = $this->createTableParameters();
        }

        return $this->tableParameters;
    }

    final public function getDefaultTableParameters(): array
    {
        $defaultTableParameters = [];
        foreach ($this->getTableParameters() as $tableParameter) {
            $defaultTableParameters[$tableParameter->getQueryParameterName()] = $tableParameter->getDefaultValue();
        }

        return $defaultTableParameters;
    }

    public function getSortConfiguration(): SortConfigurationInterface
    {
        if (null === $this->dbSortConfiguration) {
            $this->dbSortConfiguration = $this->createDbSortConfiguration();
        }

        if (null === $this->customSortConfiguration) {
            $this->customSortConfiguration = $this->createCustomSortConfiguration();
        }

        if (null !== $this->dbSortConfiguration xor null !== $this->customSortConfiguration) {
            return $this->dbSortConfiguration ?? $this->customSortConfiguration;
        }

        $errorMessage = sprintf(
            'Either %s, or %s property should be set.',
            'dbSortConfiguration',
            'customSortConfiguration'
        );

        throw new InvalidArgumentException($errorMessage);
    }

    /**
     * @return FilterRestrictionInterface[]
     */
    abstract protected function createFilterRestrictions(): array;

    /**
     * @return FilterParameterInterface[]
     */
    abstract protected function createFilterParameters(): array;

    /**
     * @return TableParameterInterface[]
     */
    abstract protected function createTableParameters(): array;

    abstract protected function createDbSortConfiguration(): ?DbSortConfigurationInterface;

    abstract protected function createCustomSortConfiguration(): ?CustomSortConfigurationInterface;
}
