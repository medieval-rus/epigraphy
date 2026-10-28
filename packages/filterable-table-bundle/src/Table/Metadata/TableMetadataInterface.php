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

namespace Vyfony\Bundle\FilterableTableBundle\Table\Metadata;

use Vyfony\Bundle\FilterableTableBundle\DataCollection\Result\DataCollectionResultInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Routing\RouteConfiguration;
use Vyfony\Bundle\FilterableTableBundle\Table\Checkbox\CheckboxHandlerInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadataInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Paginator\PaginatorInterface;

interface TableMetadataInterface
{
    public function getResultsCountText(): string;

    /**
     * @return ColumnMetadataInterface[]
     */
    public function getColumnMetadataCollection(): array;

    public function getRowDataCollection(): DataCollectionResultInterface;

    public function getListRoute(): RouteConfiguration;

    public function getShowRoute($entity): RouteConfiguration;

    public function getQueryParameters(): array;

    public function hasCheckboxColumn(): bool;

    /**
     * @return CheckboxHandlerInterface[]
     */
    public function getCheckboxHandlers(): array;

    public function hasPaginator(): bool;

    public function getPaginator(): ?PaginatorInterface;
}
