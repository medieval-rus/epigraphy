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

use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\TableParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Restriction\FilterRestrictionInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting\SortConfigurationInterface;

interface FilterConfiguratorInterface
{
    /**
     * @return FilterRestrictionInterface[]
     */
    public function getFilterRestrictions(): array;

    /**
     * @return FilterParameterInterface[]
     */
    public function getFilterParameters(): array;

    /**
     * @return TableParameterInterface[]
     */
    public function getTableParameters(): array;

    public function createSubmitButtonOptions(): array;

    public function createResetButtonOptions(): array;

    public function createSearchInFoundButtonOptions(): array;

    public function createDefaults(): array;

    public function getDefaultTableParameters(): array;

    /**
     * @param mixed $entity
     *
     * @return mixed
     */
    public function getEntityId($entity);

    public function getSortConfiguration(): SortConfigurationInterface;
}
