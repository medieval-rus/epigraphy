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
use App\Portation\Exporter\Xlsx\Accessor\XlsxAccessorInterface;
use App\Portation\Exporter\Xlsx\XlsxExporter;
use App\Portation\Exporter\Xlsx\XlsxExporterInterface;
use App\Portation\Formatter\Bool\BoolFormatterInterface;
use App\Portation\Formatter\Carrier\CarrierFormatterInterface;
use App\Portation\Importer\ImporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

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
     * @var XlsxExporterInterface
     */
    private $xlsxExporter;

    /**
     * @var XlsxAccessorInterface
     */
    private $xlsxAccessor;

    public function __construct(
        EntityManagerInterface $entityManager,
        BoolFormatterInterface $boolFormatter,
        CarrierFormatterInterface $carrierFormatter,
        WritingTypeRepository $writingTypeRepository,
        MaterialRepository $materialRepository,
        WritingMethodRepository $writingMethodRepository,
        PreservationStateRepository $preservationStateRepository,
        AlphabetRepository $alphabetRepository,
        ContentCategoryRepository $contentCategoryRepository,
        XlsxExporterInterface $xlsxExporter,
        XlsxAccessorInterface $xlsxAccessor
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
        $this->xlsxExporter = $xlsxExporter;
        $this->xlsxAccessor = $xlsxAccessor;
    }

    /**
     * @throws PhpSpreadsheetException
     */
    public function import(string $pathToFile): void
    {
        $schema = $this->xlsxExporter->getSchema();

        $reader = new Xlsx();

        $spreadsheet = $reader->load($pathToFile);

        $sheet = $spreadsheet->getActiveSheet();

        $rowIndex = 1;

        while (true) {
            $mainRowValues = $this->xlsxAccessor->readRow($rowIndex, $schema, $sheet);

            $mainId = $mainRowValues['id'];

            if ('' === $mainId) {
                break;
            }

            if (self::NEW_MAIN_ROW_ID === $mainId) {
                $inscription = new Inscription();

                foreach ($this->getMainCellHandlers() as $mainKey => $mainCellValueHandler) {
                    $mainCellValueHandler(
                        $inscription,
                        $mainRowValues[$mainKey]
                    );
                }

                while (true) {
                    $nextRowIndex = $rowIndex + 1;

                    $nestedRowValues = $this->xlsxAccessor->readRow($nextRowIndex, $schema, $sheet);

                    $nestedId = $nestedRowValues['id'];

                    if (\in_array($nestedId, ['', self::NEW_MAIN_ROW_ID], true)) {
                        break;
                    }

                    if (self::NEW_NESTED_ROW_ID === $nestedId) {
                        $interpretation = new Interpretation();

                        $inscription->addInterpretation($interpretation);

                        foreach ($this->getNestedCellHandlers() as $nestedKey => $nestedCellValueHandler) {
                            $nestedCellValueHandler(
                                $interpretation,
                                $nestedRowValues[$nestedKey]
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
            'inscription.carrier' => function (Inscription $inscription, string $formattedCarrier): void {
                $inscription->setCarrier($this->carrierFormatter->parse($formattedCarrier));
            },
            'inscription.isInSitu' => function (Inscription $inscription, string $formattedIsInSitu): void {
                $inscription->setIsInSitu($this->boolFormatter->parse(StringHelper::nullIfEmpty($formattedIsInSitu)));
            },
            'inscription.placeOnCarrier' => function (Inscription $inscription, string $formattedPlaceOnCarrier): void {
                $inscription->setPlaceOnCarrier(StringHelper::nullIfEmpty($formattedPlaceOnCarrier));
            },
            'inscription.writingType' => function (Inscription $inscription, string $formattedWritingType): void {
                $writingTypeName = StringHelper::nullIfEmpty($formattedWritingType);

                $inscription->setWritingType(
                    null === $writingTypeName
                        ? null
                        : $this->writingTypeRepository->findOneByNameOrCreate($writingTypeName)
                );
            },
            'inscription.materials' => function (Inscription $inscription, string $formattedMaterials): void {
                $formattedMaterialsParts = explode(XlsxExporter::MATERIAL_SEPARATOR, $formattedMaterials);

                foreach ($formattedMaterialsParts as $formattedMaterial) {
                    $inscription->addMaterial($this->materialRepository->findOneByNameOrCreate($formattedMaterial));
                }
            },
            'inscription.writingMethod' => function (Inscription $inscription, string $formattedWritingMethod): void {
                $writingMethodName = StringHelper::nullIfEmpty($formattedWritingMethod);

                $inscription->setWritingMethod(
                    null === $writingMethodName
                        ? null
                        : $this->writingMethodRepository->findOneByNameOrCreate($writingMethodName)
                );
            },
            'inscription.preservationState' => function (
                Inscription $inscription,
                string $formattedPreservationState
            ): void {
                $preservationStateName = StringHelper::nullIfEmpty($formattedPreservationState);

                $inscription->setPreservationState(
                    null === $preservationStateName
                        ? null
                        : $this->preservationStateRepository->findOneByNameOrCreate($preservationStateName)
                );
            },
            'inscription.alphabet' => function (Inscription $inscription, string $formattedAlphabet): void {
                $alphabetName = StringHelper::nullIfEmpty($formattedAlphabet);

                $inscription->setAlphabet(
                    null === $alphabetName
                        ? null
                        : $this->alphabetRepository->findOneByNameOrCreate($alphabetName)
                );
            },
            'inscription.contentCategory' => function (
                Inscription $inscription,
                string $formattedContentCategory
            ): void {
                $contentCategoryName = StringHelper::nullIfEmpty($formattedContentCategory);

                $inscription->setContentCategory(
                    null === $contentCategoryName
                        ? null
                        : $this->contentCategoryRepository->findOneByNameOrCreate($contentCategoryName)
                );
            },
            'inscription.dateInText' => function (Inscription $inscription, string $formattedDateInText): void {
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
            'interpretation.source' => function (Interpretation $interpretation, string $formattedSource): void {
                $interpretation->setSource(StringHelper::nullIfEmpty($formattedSource));
            },
            'interpretation.doWeAgree' => function (Interpretation $interpretation, string $formattedDoWeAgree): void {
                $interpretation->setDoWeAgree($this->boolFormatter->parse($formattedDoWeAgree));
            },
            'interpretation.text' => function (Interpretation $interpretation, string $formattedText): void {
                $interpretation->setText(StringHelper::nullIfEmpty($formattedText));
            },
            'interpretation.textImageFileName' => function (
                Interpretation $interpretation,
                string $formattedTextImageFileName
            ): void {
                $interpretation->setTextImageFileName(StringHelper::nullIfEmpty($formattedTextImageFileName));
            },
            'interpretation.transliteration' => function (
                Interpretation $interpretation,
                string $formattedTransliteration
            ): void {
                $interpretation->setTransliteration(StringHelper::nullIfEmpty($formattedTransliteration));
            },
            'interpretation.translation' => function (
                Interpretation $interpretation,
                string $formattedTranslation
            ): void {
                $interpretation->setTranslation(StringHelper::nullIfEmpty($formattedTranslation));
            },
            'interpretation.photoFileName' => function (
                Interpretation $interpretation,
                string $formattedPhotoFileName
            ): void {
                $interpretation->setPhotoFileName(StringHelper::nullIfEmpty($formattedPhotoFileName));
            },
            'interpretation.sketchFileName' => function (
                Interpretation $interpretation,
                string $formattedSketchFileName
            ): void {
                $interpretation->setSketchFileName(StringHelper::nullIfEmpty($formattedSketchFileName));
            },
            'interpretation.date' => function (Interpretation $interpretation, string $formattedDate): void {
                $interpretation->setDate(StringHelper::nullIfEmpty($formattedDate));
            },
            'interpretation.commentFileName' => function (
                Interpretation $interpretation,
                string $formattedCommentFileName
            ): void {
                $interpretation->setCommentFileName(StringHelper::nullIfEmpty($formattedCommentFileName));
            },
        ];
    }
}
