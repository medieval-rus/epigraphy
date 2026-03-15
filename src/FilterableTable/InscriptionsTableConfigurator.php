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
 * in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
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
use App\Services\Epigraphy\Localization\LocalizedTextService;
use App\Persistence\Entity\Epigraphy\Inscription;
use App\Services\Epigraphy\Stringifier\ValueStringifierInterface;
use App\Services\Epigraphy\ActualValue\Formatter\ActualValueFormatterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\FilterConfiguratorInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Routing\RouteConfiguration;
use Vyfony\Bundle\FilterableTableBundle\Table\Checkbox\CheckboxHandlerInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Configurator\AbstractTableConfigurator;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadata;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadataInterface;


final class InscriptionsTableConfigurator extends AbstractTableConfigurator
{
    private ValueStringifierInterface $valueStringifier;
    private RequestStack $requestStack;
    private LocalizedTextService $localizedTextService;

    public function __construct(
        RouterInterface $router,
        FilterConfiguratorInterface $filterConfigurator,
        ValueStringifierInterface $valueStringifier,
        RequestStack $requestStack,
        LocalizedTextService $localizedTextService
    ) {
        parent::__construct($router, $filterConfigurator);

        $this->valueStringifier = $valueStringifier;
        $this->requestStack = $requestStack;
        $this->localizedTextService = $localizedTextService;
    }

    protected function getListRoute(): RouteConfiguration
    {
        return new RouteConfiguration('inscription__longlist', []);
    }

    /**
     * @param Inscription $entity
     */
    protected function getShowRoute($entity): RouteConfiguration
    {
        return new RouteConfiguration(
            'inscription__show',
            [
                'id' => $entity->getId(),
            ]
        );
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
                ->setValueExtractor(static function (Inscription $inscription): string {
                    return (string) $inscription->getId();
                })
                ->setIsIdentifier(true)
                ->setIsSortable(false)
                ->setLabel('controller.inscription.list.table.column.id'),
            (new ColumnMetadata())
                ->setName('carrier-category')
                ->setValueExtractor(function (Inscription $inscription): string {
                    if (null === $carrier = $inscription->getCarrier()) {
                        return '';
                    }

                    $request = $this->requestStack->getCurrentRequest();
                    $locale = null !== $request ? $request->getLocale() : null;

                    return implode(
                        ', ',
                        $carrier
                            ->getCategories()
                            ->map(
                                static fn (CarrierCategory $carrierCategory) => $carrierCategory->getTranslatedName($locale)
                            )
                            ->filter(static fn ($name): bool => null !== $name && '' !== trim((string) $name))
                            ->toArray()
                    );
                })
                ->setIsIdentifier(false)
                ->setIsSortable(false)
                ->setLabel('controller.inscription.list.table.column.carrier.category'),
            (new ColumnMetadata())
                ->setName('carrier-city')
                ->setValueExtractor(function (Inscription $inscription) {
                    // on no carrier: skip
                    if (null === $carrier = $inscription->getCarrier()) {
                        return '';
                    }
                    $discoverySite = $carrier->getDiscoverySite();
                    // on no discovery site: skip
                    if (count($discoverySite) === 0) {
                        return '';
                    }
                    $cities = $discoverySite->toArray()[0]->getCities();
                    // on no cities: skip
                    if (count($cities) === 0) {
                        return '';
                    }
                    // return main name
                    $city = $cities->toArray()[0];

                    return $this->localizedTextService->resolveForEntity($city, 'name', $city->getName()) ?? '';
                })
                ->setIsIdentifier(false)
                ->setIsSortable(false)
                ->setLabel('controller.inscription.list.table.column.carrier.city'),
            (new ColumnMetadata())
                ->setName('carrier-discovery-site')
                ->setValueExtractor(function (Inscription $inscription) {
                    // on no carrier: skip
                    if (null === $carrier = $inscription->getCarrier()) {
                        return '';
                    }
                    $discoverySite = $carrier->getDiscoverySite();
                    // on no discovery site: skip
                    if (count($discoverySite) === 0) {
                        return '';
                    }
                    $site = $discoverySite->toArray()[0];

                    return $this->localizedTextService->resolveForEntity($site, 'name', $site->getName()) ?? '';
                })
                ->setIsIdentifier(false)
                ->setIsSortable(false)
                ->setLabel('controller.inscription.list.table.column.carrier.discoverySite'),
            (new ColumnMetadata())
                ->setIsRaw(true)
                ->setName('interpretation-description')
                ->setValueExtractor(function (Inscription $inscription): string {
                    return $this->valueStringifier->stringify($inscription, 'description') ?? '-';
                })
                ->setLabel('controller.inscription.list.table.column.interpretation.description'),
            (new ColumnMetadata())
                ->setIsRaw(true)
                ->setName('interpretation-text')
                ->setValueExtractor(function (Inscription $inscription): string {
                    return $this->valueStringifier->stringify(
                        $inscription,
                        'text',
                        ActualValueFormatterInterface::FORMAT_TYPE_ORIGINAL_TEXT_PLAIN
                    ) ?? '-';
                })
                ->setLabel('controller.inscription.list.table.column.interpretation.text'),
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
