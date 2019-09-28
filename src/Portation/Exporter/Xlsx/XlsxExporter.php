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

namespace App\Portation\Exporter\Xlsx;

use App\Helper\StringHelper;
use App\Persistence\Entity\Inscription\Inscription;
use App\Persistence\Entity\Inscription\Interpretation;
use App\Persistence\Entity\NamedEntityInterface;
use App\Persistence\Repository\Inscription\InscriptionRepository;
use App\Portation\Exporter\ExporterInterface;
use App\Portation\Exporter\Xlsx\Drawer\XlsxDrawerInterface;
use App\Portation\Formatter\Bool\BoolFormatterInterface;
use App\Portation\Formatter\Carrier\CarrierFormatterInterface;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// todo move all the formatting methods to FooFormatterInterfaces (as well as parsing methods in XlsxImporter)
/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class XlsxExporter implements ExporterInterface
{
    public const MATERIAL_SEPARATOR = ', ';

    public const ID_COLUMNS_COUNT = 1;

    public const MAIN_ENTITY_COLUMNS_COUNT = 10;

    public const NESTED_ENTITY_COLUMNS_COUNT = 10;

    public const NESTED_ENTITIES_GO_FIRST = false;

    /**
     * @var InscriptionRepository
     */
    private $inscriptionRepository;

    /**
     * @var BoolFormatterInterface
     */
    private $boolFormatter;

    /**
     * @var CarrierFormatterInterface
     */
    private $carrierFormatter;

    /**
     * @var XlsxDrawerInterface
     */
    private $cellDrawer;

    /**
     * @param InscriptionRepository     $inscriptionRepository
     * @param BoolFormatterInterface    $boolFormatter
     * @param CarrierFormatterInterface $carrierFormatter
     * @param XlsxDrawerInterface       $cellDrawer
     */
    public function __construct(
        InscriptionRepository $inscriptionRepository,
        BoolFormatterInterface $boolFormatter,
        CarrierFormatterInterface $carrierFormatter,
        XlsxDrawerInterface $cellDrawer
    ) {
        $this->inscriptionRepository = $inscriptionRepository;
        $this->boolFormatter = $boolFormatter;
        $this->carrierFormatter = $carrierFormatter;
        $this->cellDrawer = $cellDrawer;
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

        $getNestedCellValues = function (Interpretation $interpretation): array {
            return $this->getNestedEntityCellValues($interpretation);
        };

        $rowIndex = 1;

        foreach ($inscriptions as $entityIndex => $inscription) {
            $mainEntityCellValues = $this->getMainEntityCellValues($inscription);
            $nestedEntityCellValuesCollections = $inscription->getInterpretations()->map($getNestedCellValues);

            $idColumnIndex = 1;

            $inscriptionId = array_shift($mainEntityCellValues);

            $this->cellDrawer->drawCell($inscriptionId, $idColumnIndex, $rowIndex, $sheet);

            $mainEntityColumnIndex = $idColumnIndex + 1;
            $nestedEntityColumnIndex = $idColumnIndex + 1;

            if (self::NESTED_ENTITIES_GO_FIRST) {
                $mainEntityColumnIndex += self::NESTED_ENTITY_COLUMNS_COUNT;
            } else {
                $nestedEntityColumnIndex += self::MAIN_ENTITY_COLUMNS_COUNT;
            }

            $this->cellDrawer->drawRow($mainEntityCellValues, $mainEntityColumnIndex, $rowIndex, $sheet);

            ++$rowIndex;

            foreach ($nestedEntityCellValuesCollections as $nestedEntityCellValues) {
                $interpretationId = array_shift($nestedEntityCellValues);

                $this->cellDrawer->drawCell($interpretationId, $idColumnIndex, $rowIndex, $sheet);

                $this->cellDrawer->drawRow($nestedEntityCellValues, $nestedEntityColumnIndex, $rowIndex++, $sheet);
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
    private function getMainEntityCellValues(Inscription $inscription): array
    {
        $formatNamedEntity = function (NamedEntityInterface $namedEntity = null): ?string {
            return null === $namedEntity ? null : $namedEntity->getName();
        };

        return array_map(
            function (?string $nullableString): string {
                return StringHelper::emptyIfNull($nullableString);
            },
            [
                (string) $inscription->getId(),
                $this->carrierFormatter->format($inscription->getCarrier()),
                $this->boolFormatter->format($inscription->getIsInSitu()),
                $inscription->getPlaceOnCarrier(),
                $formatNamedEntity($inscription->getWritingType()),
                implode(self::MATERIAL_SEPARATOR, $inscription->getMaterials()->map($formatNamedEntity)->toArray()),
                $formatNamedEntity($inscription->getWritingMethod()),
                $formatNamedEntity($inscription->getPreservationState()),
                $formatNamedEntity($inscription->getAlphabet()),
                $formatNamedEntity($inscription->getContentCategory()),
                $inscription->getDateInText(),
            ]
        );
    }

    /**
     * @param Interpretation $interpretation
     *
     * @return array
     */
    private function getNestedEntityCellValues(Interpretation $interpretation): array
    {
        return array_map(
            function (?string $nullableString): string {
                return StringHelper::emptyIfNull($nullableString);
            },
            [
                sprintf('%d.%d', $interpretation->getInscription()->getId(), $interpretation->getId()),
                $interpretation->getSource(),
                $this->boolFormatter->format($interpretation->getDoWeAgree()),
                $interpretation->getText(),
                $interpretation->getTextImageFileName(),
                $interpretation->getTransliteration(),
                $interpretation->getTranslation(),
                $interpretation->getPhotoFileName(),
                $interpretation->getSketchFileName(),
                $interpretation->getDate(),
                $interpretation->getCommentFileName(),
            ]
        );
    }
}
