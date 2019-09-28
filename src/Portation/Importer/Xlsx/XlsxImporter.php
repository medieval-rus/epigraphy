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

namespace App\Portation\Importer\Xlsx;

use App\Helper\StringHelper;
use App\Persistence\Entity\Inscription\Inscription;
use App\Persistence\Entity\Inscription\Interpretation;
use App\Persistence\Repository\AlphabetRepository;
use App\Persistence\Repository\ContentCategoryRepository;
use App\Persistence\Repository\MaterialRepository;
use App\Persistence\Repository\PreservationStateRepository;
use App\Persistence\Repository\WritingMethodRepository;
use App\Persistence\Repository\WritingTypeRepository;
use App\Portation\Exporter\Xlsx\XlsxExporter;
use App\Portation\Formatter\Bool\BoolFormatterInterface;
use App\Portation\Formatter\Carrier\CarrierFormatterInterface;
use App\Portation\Importer\ImporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class XlsxImporter implements ImporterInterface
{
    private const NEW_MAIN_ROW_ID = '+';

    private const NEW_NESTED_ROW_ID = '++';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var BoolFormatterInterface
     */
    private $boolFormatter;

    /**
     * @var CarrierFormatterInterface
     */
    private $carrierFormatter;

    /**
     * @var WritingTypeRepository
     */
    private $writingTypeRepository;

    /**
     * @var MaterialRepository
     */
    private $materialRepository;

    /**
     * @var WritingMethodRepository
     */
    private $writingMethodRepository;

    /**
     * @var PreservationStateRepository
     */
    private $preservationStateRepository;

    /**
     * @var AlphabetRepository
     */
    private $alphabetRepository;

    /**
     * @var ContentCategoryRepository
     */
    private $contentCategoryRepository;

    /**
     * @param EntityManagerInterface      $entityManager
     * @param BoolFormatterInterface      $boolFormatter
     * @param CarrierFormatterInterface   $carrierFormatter
     * @param WritingTypeRepository       $writingTypeRepository
     * @param MaterialRepository          $materialRepository
     * @param WritingMethodRepository     $writingMethodRepository
     * @param PreservationStateRepository $preservationStateRepository
     * @param AlphabetRepository          $alphabetRepository
     * @param ContentCategoryRepository   $contentCategoryRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        BoolFormatterInterface $boolFormatter,
        CarrierFormatterInterface $carrierFormatter,
        WritingTypeRepository $writingTypeRepository,
        MaterialRepository $materialRepository,
        WritingMethodRepository $writingMethodRepository,
        PreservationStateRepository $preservationStateRepository,
        AlphabetRepository $alphabetRepository,
        ContentCategoryRepository $contentCategoryRepository
    ) {
        $this->entityManager = $entityManager;
        $this->boolFormatter = $boolFormatter;
        $this->carrierFormatter = $carrierFormatter;
        $this->writingTypeRepository = $writingTypeRepository;
        $this->materialRepository = $materialRepository;
        $this->writingMethodRepository = $writingMethodRepository;
        $this->preservationStateRepository = $preservationStateRepository;
        $this->alphabetRepository = $alphabetRepository;
        $this->contentCategoryRepository = $contentCategoryRepository;
    }

    /**
     * @param string $pathToFile
     *
     * @throws PhpSpreadsheetException
     */
    public function import(string $pathToFile): void
    {
        $reader = new Xlsx();

        $spreadsheet = $reader->load($pathToFile);

        $sheet = $spreadsheet->getActiveSheet();

        $rowIndex = 1;

        while (true) {
            $idCellInCurrentRowValue = $this->getIdCellValue($rowIndex, $sheet);

            if ('' === $idCellInCurrentRowValue) {
                break;
            }

            if (self::NEW_MAIN_ROW_ID === $idCellInCurrentRowValue) {
                $inscription = new Inscription();

                $mainShift = XlsxExporter::NESTED_ENTITIES_GO_FIRST
                    ? XlsxExporter::NESTED_ENTITY_COLUMNS_COUNT + 1
                    : 1;

                foreach ($this->getMainCellHandlers() as $rawMainColumnIndex => $mainCellValueHandler) {
                    $mainCellValueHandler(
                        $inscription,
                        $this->getFormattedCellValue($rowIndex, $rawMainColumnIndex + 1 + $mainShift, $sheet)
                    );
                }

                while (true) {
                    $nextRowIndex = $rowIndex + 1;

                    $idCellInNextRowValue = $this->getIdCellValue($nextRowIndex, $sheet);

                    if (\in_array($idCellInNextRowValue, ['', self::NEW_MAIN_ROW_ID], true)) {
                        break;
                    }

                    if (self::NEW_NESTED_ROW_ID === $idCellInNextRowValue) {
                        $interpretation = new Interpretation();

                        $inscription->addInterpretation($interpretation);

                        $nestedShift = XlsxExporter::NESTED_ENTITIES_GO_FIRST
                            ? 1
                            : XlsxExporter::MAIN_ENTITY_COLUMNS_COUNT + 1;

                        foreach ($this->getNestedCellHandlers() as $rawNestedColumnIndex => $nestedCellValueHandler) {
                            $nestedCellValueHandler(
                                $interpretation,
                                $this->getFormattedCellValue(
                                    $nextRowIndex,
                                    $rawNestedColumnIndex + 1 + $nestedShift,
                                    $sheet
                                )
                            );
                        }
                    }

                    ++$rowIndex;
                }

                $this->entityManager->persist($inscription);

                $this->entityManager->flush();
            }

            ++$rowIndex;
        }
    }

    /**
     * @return callable[]
     */
    private function getMainCellHandlers(): array
    {
        return [
            function (Inscription $inscription, string $formattedCarrier): void {
                $inscription->setCarrier($this->carrierFormatter->parse($formattedCarrier));
            },
            function (Inscription $inscription, string $formattedIsInSitu): void {
                $inscription->setIsInSitu($this->boolFormatter->parse(StringHelper::nullIfEmpty($formattedIsInSitu)));
            },
            function (Inscription $inscription, string $formattedPlaceOnCarrier): void {
                $inscription->setPlaceOnCarrier(StringHelper::nullIfEmpty($formattedPlaceOnCarrier));
            },
            function (Inscription $inscription, string $formattedWritingType): void {
                $writingTypeName = StringHelper::nullIfEmpty($formattedWritingType);

                $inscription->setWritingType(
                    null === $writingTypeName
                        ? null
                        : $this->writingTypeRepository->findOneByNameOrCreate($writingTypeName)
                );
            },
            function (Inscription $inscription, string $formattedMaterials): void {
                $formattedMaterialsParts = explode(XlsxExporter::MATERIAL_SEPARATOR, $formattedMaterials);

                foreach ($formattedMaterialsParts as $formattedMaterial) {
                    $inscription->addMaterial($this->materialRepository->findOneByNameOrCreate($formattedMaterial));
                }
            },
            function (Inscription $inscription, string $formattedWritingMethod): void {
                $writingMethodName = StringHelper::nullIfEmpty($formattedWritingMethod);

                $inscription->setWritingMethod(
                    null === $writingMethodName
                        ? null
                        : $this->writingMethodRepository->findOneByNameOrCreate($writingMethodName)
                );
            },
            function (Inscription $inscription, string $formattedPreservationState): void {
                $preservationStateName = StringHelper::nullIfEmpty($formattedPreservationState);

                $inscription->setPreservationState(
                    null === $preservationStateName
                        ? null
                        : $this->preservationStateRepository->findOneByNameOrCreate($preservationStateName)
                );
            },
            function (Inscription $inscription, string $formattedAlphabet): void {
                $alphabetName = StringHelper::nullIfEmpty($formattedAlphabet);

                $inscription->setAlphabet(
                    null === $alphabetName
                        ? null
                        : $this->alphabetRepository->findOneByNameOrCreate($alphabetName)
                );
            },
            function (Inscription $inscription, string $formattedContentCategory): void {
                $contentCategoryName = StringHelper::nullIfEmpty($formattedContentCategory);

                $inscription->setContentCategory(
                    null === $contentCategoryName
                        ? null
                        : $this->contentCategoryRepository->findOneByNameOrCreate($contentCategoryName)
                );
            },
            function (Inscription $inscription, string $formattedDateInText): void {
                $inscription->setDateInText(StringHelper::nullIfEmpty($formattedDateInText));
            },
        ];
    }

    /**
     * @return callable[]
     */
    private function getNestedCellHandlers(): array
    {
        return [
            function (Interpretation $interpretation, string $formattedSource): void {
                $interpretation->setSource(StringHelper::nullIfEmpty($formattedSource));
            },
            function (Interpretation $interpretation, string $formattedDoWeAgree): void {
                $interpretation->setDoWeAgree($this->boolFormatter->parse($formattedDoWeAgree));
            },
            function (Interpretation $interpretation, string $formattedText): void {
                $interpretation->setText(StringHelper::nullIfEmpty($formattedText));
            },
            function (Interpretation $interpretation, string $formattedTextImageFileName): void {
                $interpretation->setTextImageFileName(StringHelper::nullIfEmpty($formattedTextImageFileName));
            },
            function (Interpretation $interpretation, string $formattedTransliteration): void {
                $interpretation->setTransliteration(StringHelper::nullIfEmpty($formattedTransliteration));
            },
            function (Interpretation $interpretation, string $formattedTranslation): void {
                $interpretation->setTranslation(StringHelper::nullIfEmpty($formattedTranslation));
            },
            function (Interpretation $interpretation, string $formattedPhotoFileName): void {
                $interpretation->setPhotoFileName(StringHelper::nullIfEmpty($formattedPhotoFileName));
            },
            function (Interpretation $interpretation, string $formattedSketchFileName): void {
                $interpretation->setSketchFileName(StringHelper::nullIfEmpty($formattedSketchFileName));
            },
            function (Interpretation $interpretation, string $formattedDate): void {
                $interpretation->setDate(StringHelper::nullIfEmpty($formattedDate));
            },
            function (Interpretation $interpretation, string $formattedCommentFileName): void {
                $interpretation->setCommentFileName(StringHelper::nullIfEmpty($formattedCommentFileName));
            },
        ];
    }

    /**
     * @param int       $rowIndex
     * @param Worksheet $sheet
     *
     * @return string
     */
    private function getIdCellValue(int $rowIndex, Worksheet $sheet): string
    {
        return $this->getFormattedCellValue($rowIndex, 1, $sheet);
    }

    /**
     * @param int       $rowIndex
     * @param           $columnIndex
     * @param Worksheet $sheet
     *
     * @return string
     */
    private function getFormattedCellValue(int $rowIndex, $columnIndex, Worksheet $sheet): string
    {
        return $sheet->getCellByColumnAndRow($columnIndex, $rowIndex)->getFormattedValue();
    }
}
