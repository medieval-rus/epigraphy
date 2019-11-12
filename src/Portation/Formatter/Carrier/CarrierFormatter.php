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

namespace App\Portation\Formatter\Carrier;

use App\Persistence\Entity\Carrier\Carrier;
use App\Persistence\Entity\Carrier\ItemCarrier;
use App\Persistence\Entity\Carrier\MonumentCarrier;
use App\Persistence\Entity\Carrier\WallCarrier;
use App\Persistence\Repository\Carrier\ItemCarrierRepository;
use App\Persistence\Repository\Carrier\MonumentCarrierRepository;
use App\Persistence\Repository\Carrier\WallCarrierRepository;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use ReflectionObject;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CarrierFormatter implements CarrierFormatterInterface
{
    public const ITEM_CARRIER_NO_NAME = '-';

    public const MONUMENT_CARRIER_NO_NAME = '-';

    public const WALL_CARRIER_NO_BUILDING_DATA = '-';

    public const WALL_CARRIER_DISCRIMINATOR = 'wall';

    public const ITEM_CARRIER_DISCRIMINATOR = 'item';

    public const MONUMENT_CARRIER_DISCRIMINATOR = 'monument';

    public const CARRIER_DISCRIMINATOR_POSTFIX = ': ';

    private const REGEX_DELIMITER = '/';

    /**
     * @var WallCarrierRepository
     */
    private $wallCarrierRepository;

    /**
     * @var ItemCarrierRepository
     */
    private $itemCarrierRepository;

    /**
     * @var MonumentCarrierRepository
     */
    private $monumentCarrierRepository;

    public function __construct(
        WallCarrierRepository $wallCarrierRepository,
        ItemCarrierRepository $itemCarrierRepository,
        MonumentCarrierRepository $monumentCarrierRepository
    ) {
        $this->wallCarrierRepository = $wallCarrierRepository;
        $this->itemCarrierRepository = $itemCarrierRepository;
        $this->monumentCarrierRepository = $monumentCarrierRepository;
    }

    public function format(?Carrier $carrier): ?string
    {
        if (null === $carrier) {
            return null;
        }

        return sprintf(
            '%s%s%s',
            $this->getCarrierDiscriminator($carrier),
            self::CARRIER_DISCRIMINATOR_POSTFIX,
            $this->formatCarrierValue($carrier)
        );
    }

    /**
     * @throws ORMException
     */
    public function parse(string $formattedCarrier): ?Carrier
    {
        if ('' === $formattedCarrier) {
            return null;
        }

        $carrierDiscriminators = array_map(
            function (string $value): string {
                return preg_quote($value, self::REGEX_DELIMITER);
            },
            [
                self::WALL_CARRIER_DISCRIMINATOR,
                self::ITEM_CARRIER_DISCRIMINATOR,
                self::MONUMENT_CARRIER_DISCRIMINATOR,
            ]
        );

        $carrierDiscriminatorPostfix = preg_quote(
            self::CARRIER_DISCRIMINATOR_POSTFIX,
            self::REGEX_DELIMITER
        );

        $regexPattern =
            self::REGEX_DELIMITER
            .'('.implode('|', $carrierDiscriminators).')'
            .$carrierDiscriminatorPostfix
            .'([^;]+)(?:; ([^;]+)(?:; ([^;]+))?)?'
            .self::REGEX_DELIMITER;

        if (1 === preg_match($regexPattern, $formattedCarrier, $matches)) {
            $carrierDiscriminator = $matches[1];

            switch ($carrierDiscriminator) {
                case self::WALL_CARRIER_DISCRIMINATOR:
                    $buildingTypeNameMatchIndex = 2;
                    $buildingNameMatchIndex = 3;

                    $buildingTypeName = $matches[$buildingTypeNameMatchIndex];
                    $buildingName = \count($matches) <= $buildingNameMatchIndex
                        ? null
                        : $matches[$buildingNameMatchIndex];

                    return $this->wallCarrierRepository->findOneOrCreate(
                        $buildingTypeName,
                        $buildingName
                    );
                case self::ITEM_CARRIER_DISCRIMINATOR:
                    $carrierName = $matches[2];

                    return $this->itemCarrierRepository->findOneByNameOrCreate($carrierName);
                case self::MONUMENT_CARRIER_DISCRIMINATOR:
                    $carrierName = $matches[2];

                    return $this->monumentCarrierRepository->findOneByNameOrCreate($carrierName);
                default:
                    $message = sprintf('Invalid carrier discriminator "%s"', $carrierDiscriminator);

                    throw new InvalidArgumentException($message);
            }
        } else {
            throw new InvalidArgumentException(sprintf('Invalid formatted carrier "%s"', $formattedCarrier));
        }
    }

    private function getCarrierDiscriminator(Carrier $carrier): string
    {
        switch (true) {
            case $carrier instanceof WallCarrier:
                return self::WALL_CARRIER_DISCRIMINATOR;
            case $carrier instanceof ItemCarrier:
                return self::ITEM_CARRIER_DISCRIMINATOR;
            case $carrier instanceof MonumentCarrier:
                return self::MONUMENT_CARRIER_DISCRIMINATOR;
            default:
                $message = sprintf('Unknown carrier type "%s"', (new ReflectionObject($carrier))->getName());

                throw new InvalidArgumentException($message);
        }
    }

    private function formatCarrierValue(Carrier $carrier): string
    {
        switch (true) {
            case $carrier instanceof WallCarrier:
                return $this->formatWallCarrier($carrier);
            case $carrier instanceof ItemCarrier:
                return $this->formatItemCarrier($carrier);
            case $carrier instanceof MonumentCarrier:
                return $this->formatMonumentCarrier($carrier);
            default:
                $message = sprintf('Unknown carrier type "%s"', (new ReflectionObject($carrier))->getName());

                throw new InvalidArgumentException($message);
        }
    }

    /**
     * @return string|null
     */
    private function formatWallCarrier(WallCarrier $carrier): string
    {
        $building = $carrier->getBuilding();

        if (null === $building) {
            return self::WALL_CARRIER_NO_BUILDING_DATA;
        }

        $buildingTypeName = $building->getType()->getName();

        $buildingName = $building->getName();

        if (null === $buildingName) {
            return sprintf('%s', $buildingTypeName);
        }

        return sprintf('%s; %s', $buildingTypeName, $buildingName);
    }

    private function formatItemCarrier(ItemCarrier $carrier): string
    {
        return sprintf('%s', $carrier->getName() ?? self::ITEM_CARRIER_NO_NAME);
    }

    private function formatMonumentCarrier(MonumentCarrier $carrier): string
    {
        return sprintf('%s', $carrier->getName() ?? self::MONUMENT_CARRIER_NO_NAME);
    }
}
