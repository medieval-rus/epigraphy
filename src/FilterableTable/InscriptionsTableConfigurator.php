<?php

declare(strict_types=1);

/*
 * This file is part of «Epigraphy of Medieval Rus'» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus'» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus'» database is distributed
 * in the hope  that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus'» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\FilterableTable;

use Vyfony\Bundle\FilterableTableBundle\Table\Checkbox\CheckboxHandlerInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Configurator\AbstractTableConfigurator;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadata;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadataInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class InscriptionsTableConfigurator extends AbstractTableConfigurator
{
    protected function getResultsCountText(): string
    {
        return 'controller.inscription.list.table.resultsCount';
    }

    /**
     * @return ColumnMetadataInterface[]
     */
    protected function createColumnMetadataCollection(): array
    {
        return [
            (new ColumnMetadata())
                ->setName('id')
                ->setIsIdentifier(true)
                ->setIsSortable(true)
                ->setLabel('controller.inscription.list.table.column.id'),
            (new ColumnMetadata())
                ->setName('text')
                ->setLabel('controller.inscription.list.table.column.text'),
        ];
    }

    /**
     * @return CheckboxHandlerInterface[]
     */
    protected function createCheckboxHandlers(): array
    {
        return [];
    }
}
