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

namespace Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column;

interface ColumnMetadataInterface
{
    public function getName(): string;

    public function getLabel(): string;

    public function getIsSortable(): bool;

    public function setSortParameters(array $sortParameters): self;

    public function getSortParameters(): ?array;

    public function getValue(object $rowData): string;

    public function getAttributes(): array;

    public function getIsRaw(): bool;
}
