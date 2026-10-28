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

namespace Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting;

final class DbSortConfiguration implements DbSortConfigurationInterface
{
    private string $defaultSortBy;
    private bool $isDefaultSortAsc;
    private int $pageSize;
    private int $paginatorTailLength;
    private string $disablePaginationLabel;
    private array $allowedSortFields;

    public function __construct(
        string $defaultSortBy,
        bool $isDefaultSortAsc,
        int $pageSize,
        int $paginatorTailLength,
        string $disablePaginationLabel,
        array $allowedSortFields = []
    ) {
        $this->defaultSortBy = $defaultSortBy;
        $this->isDefaultSortAsc = $isDefaultSortAsc;
        $this->pageSize = $pageSize;
        $this->paginatorTailLength = $paginatorTailLength;
        $this->disablePaginationLabel = $disablePaginationLabel;
        $this->allowedSortFields = array_values(array_unique(array_merge([$defaultSortBy], $allowedSortFields)));
        foreach ($this->allowedSortFields as $field) {
            if (!is_string($field) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $field)) {
                throw new \InvalidArgumentException('Sort fields must be simple mapped field names.');
            }
        }
    }

    public function getDefaultSortBy(): string
    {
        return $this->defaultSortBy;
    }

    public function getAllowedSortFields(): array
    {
        return $this->allowedSortFields;
    }

    public function getIsDefaultSortAsc(): bool
    {
        return $this->isDefaultSortAsc;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getPaginatorTailLength(): int
    {
        return $this->paginatorTailLength;
    }

    public function getDisablePaginationLabel(): string
    {
        return $this->disablePaginationLabel;
    }
}
