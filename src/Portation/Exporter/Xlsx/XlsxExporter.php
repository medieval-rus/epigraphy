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
use App\Portation\Exporter\Xlsx\Accessor\XlsxAccessorInterface;
use App\Portation\Formatter\Bool\BoolFormatterInterface;
use App\Portation\Formatter\Carrier\CarrierFormatterInterface;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class XlsxExporter implements XlsxExporterInterface, ExporterInterface
{
    public const MATERIAL_SEPARATOR = ', ';

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
     * @var XlsxAccessorInterface
     */
    private $xlsxAccessor;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param InscriptionRepository     $inscriptionRepository
     * @param BoolFormatterInterface    $boolFormatter
     * @param CarrierFormatterInterface $carrierFormatter
     * @param XlsxAccessorInterface     $xlsxAccessor
     * @param TranslatorInterface       $translator
     */
    public function __construct(
        InscriptionRepository $inscriptionRepository,
        BoolFormatterInterface $boolFormatter,
        CarrierFormatterInterface $carrierFormatter,
        XlsxAccessorInterface $xlsxAccessor,
        TranslatorInterface $translator
    ) {
        $this->inscriptionRepository = $inscriptionRepository;
        $this->boolFormatter = $boolFormatter;
        $this->carrierFormatter = $carrierFormatter;
        $this->xlsxAccessor = $xlsxAccessor;
        $this->translator = $translator;
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
     * @return string[]
     */
    public function getSchema(): array
    {
        return [
            'id',
            'inscription.carrier',
            'inscription.isInSitu',
            'inscription.placeOnCarrier',
            'inscription.writingType',
            'inscription.materials',
            'inscription.writingMethod',
            'inscription.preservationState',
            'inscription.alphabet',
            'inscription.contentCategory',
            'inscription.dateInText',
            'interpretation.source',
            'interpretation.doWeAgree',
            'interpretation.text',
            'interpretation.textImageFileName',
            'interpretation.transliteration',
            'interpretation.translation',
            'interpretation.photoFileName',
            'interpretation.sketchFileName',
            'interpretation.date',
            'interpretation.commentFileName',
        ];
    }

    /**
     * @param Inscription[] $inscriptions
     * @param string        $pathToFile
     *
     * @throws PhpSpreadsheetException
     */
    private function exportInscriptions(array $inscriptions, string $pathToFile): void
    {
        $schema = $this->getSchema();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $getNestedCellValues = function (Interpretation $interpretation): array {
            return $this->getNestedEntityCellValues($interpretation);
        };

        $rowIndex = 0;

        $this->drawHeader($schema, $rowIndex++, $sheet);

        foreach ($inscriptions as $entityIndex => $inscription) {
            $mainEntityCellValues = $this->getMainEntityCellValues($inscription);
            $nestedEntityCellValuesCollections = $inscription->getInterpretations()->map($getNestedCellValues);

            $this->xlsxAccessor->writeRow($mainEntityCellValues, $rowIndex++, $schema, $sheet);

            foreach ($nestedEntityCellValuesCollections as $nestedEntityCellValues) {
                $this->xlsxAccessor->writeRow($nestedEntityCellValues, $rowIndex++, $schema, $sheet);
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
                'id' => (string) $inscription->getId(),
                'inscription.carrier' => $this->carrierFormatter->format($inscription->getCarrier()),
                'inscription.isInSitu' => $this->boolFormatter->format($inscription->getIsInSitu()),
                'inscription.placeOnCarrier' => $inscription->getPlaceOnCarrier(),
                'inscription.writingType' => $formatNamedEntity($inscription->getWritingType()),
                'inscription.materials' => implode(
                    self::MATERIAL_SEPARATOR,
                    $inscription->getMaterials()->map($formatNamedEntity)->toArray()
                ),
                'inscription.writingMethod' => $formatNamedEntity($inscription->getWritingMethod()),
                'inscription.preservationState' => $formatNamedEntity($inscription->getPreservationState()),
                'inscription.alphabet' => $formatNamedEntity($inscription->getAlphabet()),
                'inscription.contentCategory' => $formatNamedEntity($inscription->getContentCategory()),
                'inscription.dateInText' => $inscription->getDateInText(),
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
                'id' => sprintf(
                    '%d.%d',
                    $interpretation->getInscription()->getId(),
                    $interpretation->getId()
                ),
                'interpretation.source' => $interpretation->getSource(),
                'interpretation.doWeAgree' => $this->boolFormatter->format($interpretation->getDoWeAgree()),
                'interpretation.text' => $interpretation->getText(),
                'interpretation.textImageFileName' => $interpretation->getTextImageFileName(),
                'interpretation.transliteration' => $interpretation->getTransliteration(),
                'interpretation.translation' => $interpretation->getTranslation(),
                'interpretation.photoFileName' => $interpretation->getPhotoFileName(),
                'interpretation.sketchFileName' => $interpretation->getSketchFileName(),
                'interpretation.date' => $interpretation->getDate(),
                'interpretation.commentFileName' => $interpretation->getCommentFileName(),
            ]
        );
    }

    /**
     * @param array     $schema
     * @param int       $rowIndex
     * @param Worksheet $sheet
     */
    private function drawHeader(array $schema, int $rowIndex, Worksheet $sheet): void
    {
        $convertSchemaValueToTranslationKey = function (string $schemaValue): string {
            return sprintf('portation.xlsx.header.%s', $schemaValue);
        };

        $translate = function (string $key): string {
            return $this->translator->trans($key, [], 'portation');
        };

        $this->xlsxAccessor->writeRow(
            array_combine(
                $schema,
                array_map(
                    $translate,
                    array_map(
                        $convertSchemaValueToTranslationKey,
                        $schema
                    )
                )
            ),
            $rowIndex,
            $schema,
            $sheet
        );
    }
}
