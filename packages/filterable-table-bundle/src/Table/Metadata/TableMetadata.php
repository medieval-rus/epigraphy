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

final class TableMetadata implements TableMetadataInterface
{
    /**
     * @var string
     */
    private $resultsCountText;

    /**
     * @var ColumnMetadataInterface[]
     */
    private $columnMetadataCollection;

    /**
     * @var DataCollectionResultInterface
     */
    private $rowDataCollection;

    /**
     * @var RouteConfiguration
     */
    private $listRoute;

    /**
     * @var callable
     */
    private $showRouteGetter;

    /**
     * @var array
     */
    private $queryParameters;

    /**
     * @var CheckboxHandlerInterface[]
     */
    private $checkboxHandlers;

    /**
     * @var PaginatorInterface|null
     */
    private $paginator;

    /**
     * @param ColumnMetadataInterface[]  $columnMetadataCollection
     * @param CheckboxHandlerInterface[] $checkboxHandlers
     */
    public function __construct(
        string $resultsCountText,
        array $columnMetadataCollection,
        DataCollectionResultInterface $rowDataCollection,
        RouteConfiguration $listRoute,
        callable $showRouteGetter,
        array $queryParameters,
        array $checkboxHandlers,
        ?PaginatorInterface $paginator
    ) {
        $this->resultsCountText = $resultsCountText;
        $this->columnMetadataCollection = $columnMetadataCollection;
        $this->rowDataCollection = $rowDataCollection;
        $this->listRoute = $listRoute;
        $this->showRouteGetter = $showRouteGetter;
        $this->queryParameters = $queryParameters;
        $this->checkboxHandlers = $checkboxHandlers;
        $this->paginator = $paginator;
    }

    public function getResultsCountText(): string
    {
        return $this->resultsCountText;
    }

    /**
     * @return ColumnMetadataInterface[]
     */
    public function getColumnMetadataCollection(): array
    {
        return $this->columnMetadataCollection;
    }

    public function getRowDataCollection(): DataCollectionResultInterface
    {
        return $this->rowDataCollection;
    }

    public function getListRoute(): RouteConfiguration
    {
        return $this->listRoute;
    }

    public function getShowRoute($entity): RouteConfiguration
    {
        return \call_user_func_array($this->showRouteGetter, [$entity]);
    }

    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    public function hasCheckboxColumn(): bool
    {
        return \count($this->checkboxHandlers) > 0;
    }

    /**
     * @return CheckboxHandlerInterface[]
     */
    public function getCheckboxHandlers(): array
    {
        return $this->checkboxHandlers;
    }

    public function hasPaginator(): bool
    {
        return null !== $this->paginator;
    }

    public function getPaginator(): ?PaginatorInterface
    {
        return $this->paginator;
    }
}
