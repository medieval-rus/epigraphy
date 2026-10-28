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

interface DbSortConfigurationInterface extends SortConfigurationInterface
{
    public function getDefaultSortBy(): string;

    /** @return string[] */
    public function getAllowedSortFields(): array;

    public function getIsDefaultSortAsc(): bool;

    public function getPageSize(): int;

    public function getPaginatorTailLength(): int;

    public function getDisablePaginationLabel(): string;
}
