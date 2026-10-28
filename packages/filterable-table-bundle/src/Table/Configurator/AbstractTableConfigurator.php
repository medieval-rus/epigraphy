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

namespace Vyfony\Bundle\FilterableTableBundle\Table\Configurator;

use Symfony\Component\Routing\RouterInterface;
use Vyfony\Bundle\FilterableTableBundle\DataCollection\Result\DataCollectionResultInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\FilterConfiguratorInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Routing\RouteConfiguration;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting\DbSortConfigurationInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Checkbox\CheckboxHandlerInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadataInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\TableMetadata;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\TableMetadataInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Paginator\Page\Page;
use Vyfony\Bundle\FilterableTableBundle\Table\Paginator\Page\PageInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Paginator\Paginator;
use Vyfony\Bundle\FilterableTableBundle\Table\Paginator\PaginatorInterface;

abstract class AbstractTableConfigurator implements TableConfiguratorInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FilterConfiguratorInterface
     */
    private $filterConfigurator;

    public function __construct(
        RouterInterface $router,
        FilterConfiguratorInterface $filterConfigurator
    ) {
        $this->router = $router;
        $this->filterConfigurator = $filterConfigurator;
    }

    public function getTableMetadata(
        DataCollectionResultInterface $dataCollectionResult,
        array $queryParameters
    ): TableMetadataInterface {
        $sortConfiguration = $this->filterConfigurator->getSortConfiguration();

        return new TableMetadata(
            $this->getResultsCountText(),
            $this->getColumnMetadataCollection($queryParameters),
            $dataCollectionResult,
            $this->getListRoute(),
            function ($entity): RouteConfiguration {
                return $this->getShowRoute($entity);
            },
            $queryParameters,
            $this->createCheckboxHandlers(),
            $sortConfiguration instanceof DbSortConfigurationInterface && $dataCollectionResult->getHasPagination()
                ? $this->createPaginator($dataCollectionResult->getDataCount(), $queryParameters, $sortConfiguration)
                : null
        );
    }

    public function getDefaultTableParameters(): array
    {
        $sortConfiguration = $this->filterConfigurator->getSortConfiguration();

        if ($sortConfiguration instanceof DbSortConfigurationInterface) {
            return [
                'sortBy' => $sortConfiguration->getDefaultSortBy(),
                'sortOrder' => $sortConfiguration->getIsDefaultSortAsc() ? 'asc' : 'desc',
                'page' => '1',
            ];
        }

        return [];
    }

    abstract protected function getListRoute(): RouteConfiguration;

    /**
     * @param mixed $entity
     */
    abstract protected function getShowRoute($entity): RouteConfiguration;

    abstract protected function getResultsCountText(): string;

    /**
     * @return ColumnMetadataInterface[]
     */
    abstract protected function createColumnMetadataCollection(): array;

    /**
     * @return CheckboxHandlerInterface[]
     */
    abstract protected function createCheckboxHandlers(): array;

    /**
     * @return ColumnMetadataInterface[]
     */
    private function getColumnMetadataCollection(array $queryParameters): array
    {
        $columnMetadataCollection = array_merge(
            $this->createColumnMetadataCollection(),
            $this->createFilterDependentColumnMetadataCollection($queryParameters)
        );

        $this->applySortParameters($columnMetadataCollection, $queryParameters);

        return $columnMetadataCollection;
    }

    /**
     * @return ColumnMetadataInterface[]
     */
    private function createFilterDependentColumnMetadataCollection(array $queryParameters): array
    {
        $filterDependentColumnMetadataCollection = [];

        foreach ($this->filterConfigurator->getTableParameters() as $tableParameter) {
            $filterDependentColumnMetadataCollection[] = $tableParameter->getColumnMetadataCollection($queryParameters);
        }

        if (\count($filterDependentColumnMetadataCollection) > 0) {
            return array_merge(...$filterDependentColumnMetadataCollection);
        }

        return [];
    }

    /**
     * @param ColumnMetadataInterface[] $columnMetadataCollection
     */
    private function applySortParameters(array $columnMetadataCollection, array $queryParameters): void
    {
        foreach ($columnMetadataCollection as $columnMetadata) {
            if ($columnMetadata->getIsSortable()) {
                $columnMetadata->setSortParameters(
                    $this->createSortParametersForColumn($columnMetadata->getName(), $queryParameters)
                );
            }
        }
    }

    private function createSortParametersForColumn(string $columnName, array $formData): array
    {
        $sortBy = $columnName;
        $sortOrder = 'desc' === $formData['sortOrder'] || $columnName !== $formData['sortBy'] ? 'asc' : 'desc';

        $formData['sortBy'] = $sortBy;
        $formData['sortOrder'] = $sortOrder;

        return $formData;
    }

    private function createPaginator(
        int $totalRowsCount,
        array $queryParameters,
        DbSortConfigurationInterface $sortConfiguration
    ): PaginatorInterface {
        $pagesCount = (int) ceil($totalRowsCount / $sortConfiguration->getPageSize());

        $pages = array_fill(1, $pagesCount, null);

        array_walk($pages, function (&$page, int $pageIndex, array $queryParameters): void {
            $page = $this->createPage($pageIndex, $queryParameters);
        }, $queryParameters);

        return new Paginator($sortConfiguration->getPaginatorTailLength(), (int) $queryParameters['page'], $pages);
    }

    private function createPage(int $pageIndex, array $formData): PageInterface
    {
        $formData['page'] = (string) $pageIndex;

        return new Page(
            $pageIndex,
            $this->router->generate($this->getListRoute()->getName(), $formData)
        );
    }
}
