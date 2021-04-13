<?php

declare(strict_types=1);

/*
 * This file is part of «Epigraphy of Medieval Rus» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus» database is distributed
 * in the hope  that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\FilterableTable;

use App\Persistence\Entity\Epigraphy\CarrierCategory;
use App\Persistence\Entity\Epigraphy\Inscription;
use App\Services\Stringifier\ValueStringifierInterface;
use Symfony\Component\Routing\RouterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\FilterConfiguratorInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Checkbox\CheckboxHandlerInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Configurator\AbstractTableConfigurator;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadata;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadataInterface;

final class InscriptionsTableConfigurator extends AbstractTableConfigurator
{
    /**
     * @var ValueStringifierInterface
     */
    private $valueStringifier;

    public function __construct(
        RouterInterface $router,
        FilterConfiguratorInterface $filterConfigurator,
        string $defaultSortBy,
        string $defaultSortOrder,
        string $listRoute,
        string $showRoute,
        array $showRouteParameters,
        int $pageSize,
        int $paginatorTailLength,
        ValueStringifierInterface $valueStringifier
    ) {
        parent::__construct(
            $router,
            $filterConfigurator,
            $defaultSortBy,
            $defaultSortOrder,
            $listRoute,
            $showRoute,
            $showRouteParameters,
            $pageSize,
            $paginatorTailLength
        );

        $this->valueStringifier = $valueStringifier;
    }

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
                ->setName('number')
                ->setLabel('controller.inscription.list.table.column.number')
                ->setValueExtractor(static function (Inscription $inscription): string {
                    return $inscription->getNumber() ?? '';
                }),
            (new ColumnMetadata())
                ->setName('carrier-category')
                ->setValueExtractor(static function (Inscription $inscription): string {
                    return implode(
                        ', ',
                        $inscription
                            ->getCarrier()
                            ->getCategories()
                            ->map(
                                static function (CarrierCategory $carrierCategory): string {
                                    return $carrierCategory->getName();
                                }
                            )
                            ->toArray()
                    );
                })
                ->setIsIdentifier(false)
                ->setIsSortable(false)
                ->setLabel('controller.inscription.list.table.column.carrier.category'),
            (new ColumnMetadata())
                ->setName('carrier-origin1')
                ->setValueExtractor(function (Inscription $inscription): string {
                    return (string) $inscription->getCarrier()->getOrigin1();
                })
                ->setIsIdentifier(false)
                ->setIsSortable(false)
                ->setLabel('controller.inscription.list.table.column.carrier.origin1'),
            (new ColumnMetadata())
                ->setName('interpretation-contentCategories')
                ->setValueExtractor(function (Inscription $inscription): string {
                    return $this->valueStringifier->stringify($inscription, 'contentCategories') ?? '-';
                })
                ->setIsIdentifier(false)
                ->setIsSortable(false)
                ->setLabel('controller.inscription.list.table.column.interpretation.contentCategory'),
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
