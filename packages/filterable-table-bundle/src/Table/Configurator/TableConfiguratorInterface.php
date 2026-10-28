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

use Vyfony\Bundle\FilterableTableBundle\DataCollection\Result\DataCollectionResultInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\TableMetadataInterface;

interface TableConfiguratorInterface
{
    public function getTableMetadata(
        DataCollectionResultInterface $dataCollectionResult,
        array $queryParameters
    ): TableMetadataInterface;

    public function getDefaultTableParameters(): array;
}
