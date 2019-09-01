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

namespace App\Portation\Exporter;

use App\Persistence\Entity\Carrier\Carrier;
use App\Persistence\Entity\Carrier\ItemCarrier;
use App\Persistence\Entity\Carrier\MonumentCarrier;
use App\Persistence\Entity\Carrier\WallCarrier;
use App\Persistence\Entity\Inscription;
use App\Persistence\Entity\Material;
use App\Persistence\Entity\NamedEntityInterface;
use App\Persistence\Repository\InscriptionRepository;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ReflectionObject;

// todo move all the formatting methods to FooFormatterInterfaces (as well as parsing methods in XlsxImporter)
/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class XlsxExporter implements ExporterInterface
{
    public const NO_CARRIER_NAME = '-';

    public const WALL_CARRIER_DISCRIMINATOR = 'wall';

    public const ITEM_CARRIER_DISCRIMINATOR = 'item';

    public const MONUMENT_CARRIER_DISCRIMINATOR = 'monument';

    public const MATERIAL_SEPARATOR = ', ';

    public const IS_IN_SITU_YES = 'да';

    public const IS_IN_SITU_NO = 'нет';

    /**
     * @var InscriptionRepository
     */
    private $inscriptionRepository;

    /**
     * @param InscriptionRepository $inscriptionRepository
     */
    public function __construct(InscriptionRepository $inscriptionRepository)
    {
        $this->inscriptionRepository = $inscriptionRepository;
    }

    /**
     * @param string   $pathToFile
     * @param int|null $bunchSize
     *
     * @throws InvalidArgumentException
     * @throws PhpSpreadsheetException
     */
    public function export(string $pathToFile, ?int $bunchSize): void
    {
        if (null !== $bunchSize && $bunchSize <= 0) {
            throw new InvalidArgumentException('Bunch size can only be null or greater than zero');
        }

        $inscriptions = $this->inscriptionRepository->findAll();

        if (null === $bunchSize) {
            $this->exportInscriptions($inscriptions, $pathToFile);
        } else {
            foreach (array_chunk($inscriptions, $bunchSize) as $bunchIndex => $bunch) {
                $this->exportInscriptions($bunch, $this->getBunchPathToFile($pathToFile, $bunchIndex));
            }
        }
    }

    /**
     * @param Inscription[] $inscriptions
     * @param string        $pathToFile
     *
     * @throws PhpSpreadsheetException
     */
    private function exportInscriptions(array $inscriptions, string $pathToFile): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($inscriptions as $entityIndex => $inscription) {
            $rowIndex = $entityIndex + 1;

            foreach ($this->getColumnValues($inscription) as $rawColumnIndex => $columnValue) {
                $columnIndex = $rawColumnIndex + 1;

                $sheet->getColumnDimensionByColumn($columnIndex)->setAutoSize(true);

                $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $columnValue);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($pathToFile);
    }

    /**
     * @param string $pathToFile
     * @param int    $bunchIndex
     *
     * @return string
     */
    private function getBunchPathToFile(string $pathToFile, int $bunchIndex): string
    {
        $pathToFileParts = explode('.', $pathToFile);

        $formattedBunchIndex = (string) ($bunchIndex + 1);

        if (\count($pathToFileParts) > 1) {
            $extension = array_pop($pathToFileParts);

            $pathToFileParts[] = $formattedBunchIndex;
            $pathToFileParts[] = $extension;
        } else {
            $pathToFileParts[] = $formattedBunchIndex;
        }

        return implode('.', $pathToFileParts);
    }

    /**
     * @param Inscription $inscription
     *
     * @return array
     */
    private function getColumnValues(Inscription $inscription): array
    {
        $formatMaterial = function (Material $material): string {
            return $this->formatNamedEntity($material);
        };

        $formatNullableString = function (?string $nullableString): string {
            if (null === $nullableString) {
                return '';
            }

            return $nullableString;
        };

        $nullableValues = [
            (string) $inscription->getId(),
            $this->formatCarrier($inscription->getCarrier()),
            $inscription->getIsInSitu() ? self::IS_IN_SITU_YES : self::IS_IN_SITU_NO,
            $inscription->getPlaceOnCarrier(),
            $this->formatNamedEntity($inscription->getWritingType()),
            implode(self::MATERIAL_SEPARATOR, $inscription->getMaterials()->map($formatMaterial)->toArray()),
            $this->formatNamedEntity($inscription->getWritingMethod()),
            $this->formatNamedEntity($inscription->getPreservationState()),
            $this->formatNamedEntity($inscription->getAlphabet()),
            $inscription->getText(),
            $inscription->getNewText(),
            $inscription->getTransliteration(),
            $inscription->getTranslation(),
            $this->formatNamedEntity($inscription->getContentCategory()),
            $inscription->getDateInText(),
            $inscription->getCommentOnDate(),
            $inscription->getCommentOnText(),
        ];

        return array_map($formatNullableString, $nullableValues);
    }

    /**
     * @param NamedEntityInterface|null $namedEntity
     *
     * @return string|null
     */
    private function formatNamedEntity(NamedEntityInterface $namedEntity = null): ?string
    {
        return null === $namedEntity ? null : $namedEntity->getName();
    }

    /**
     * @param Carrier|null $carrier
     *
     * @return string|null
     */
    private function formatCarrier(?Carrier $carrier): ?string
    {
        if (null === $carrier) {
            return null;
        }

        switch (true) {
            case $carrier instanceof WallCarrier:
                return sprintf('%s: %s', self::WALL_CARRIER_DISCRIMINATOR, $this->formatWallCarrier($carrier));
            case $carrier instanceof ItemCarrier:
                return sprintf('%s: %s', self::ITEM_CARRIER_DISCRIMINATOR, $this->formatItemCarrier($carrier));
            case $carrier instanceof MonumentCarrier:
                return sprintf('%s: %s', self::MONUMENT_CARRIER_DISCRIMINATOR, $this->formatMonumentCarrier($carrier));
            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'Unknown carrier type "%s"',
                        (new ReflectionObject($carrier))->getName()
                    )
                );
        }
    }

    /**
     * @param WallCarrier $carrier
     *
     * @return string|null
     */
    private function formatWallCarrier(WallCarrier $carrier): string
    {
        $building = $carrier->getBuilding();

        $carrierName = $this->formatCarrierName($carrier->getName());

        if (null === $building) {
            return sprintf('%s', $carrierName);
        }

        $buildingTypeName = $building->getBuildingType()->getName();

        $buildingName = $building->getName();

        if (null === $buildingName) {
            return sprintf('%s; %s', $carrierName, $buildingTypeName);
        }

        return sprintf('%s; %s; %s', $carrierName, $buildingTypeName, $buildingName);
    }

    /**
     * @param ItemCarrier $carrier
     *
     * @return string
     */
    private function formatItemCarrier(ItemCarrier $carrier): string
    {
        return sprintf('%s', $this->formatCarrierName($carrier->getName()));
    }

    /**
     * @param MonumentCarrier $carrier
     *
     * @return string
     */
    private function formatMonumentCarrier(MonumentCarrier $carrier): string
    {
        return sprintf('%s', $this->formatCarrierName($carrier->getName()));
    }

    /**
     * @param string|null $carrierName
     *
     * @return string
     */
    private function formatCarrierName(?string $carrierName): string
    {
        return $carrierName ?? self::NO_CARRIER_NAME;
    }
}
