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

namespace App\Portation\Importer;

use App\Persistence\Entity\Inscription;
use App\Persistence\Entity\NamedEntityInterface;
use App\Persistence\Repository\AlphabetRepository;
use App\Persistence\Repository\Carrier\ItemCarrierRepository;
use App\Persistence\Repository\Carrier\MonumentCarrierRepository;
use App\Persistence\Repository\Carrier\WallCarrierRepository;
use App\Persistence\Repository\ContentCategoryRepository;
use App\Persistence\Repository\MaterialRepository;
use App\Persistence\Repository\NamedEntityRepository;
use App\Persistence\Repository\PreservationStateRepository;
use App\Persistence\Repository\WritingMethodRepository;
use App\Persistence\Repository\WritingTypeRepository;
use App\Portation\Exporter\XlsxExporter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class XlsxImporter implements ImporterInterface
{
    private const NEW_ROW_ID = '+';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

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
     * @param WallCarrierRepository       $wallCarrierRepository
     * @param ItemCarrierRepository       $itemCarrierRepository
     * @param MonumentCarrierRepository   $monumentCarrierRepository
     * @param WritingTypeRepository       $writingTypeRepository
     * @param MaterialRepository          $materialRepository
     * @param WritingMethodRepository     $writingMethodRepository
     * @param PreservationStateRepository $preservationStateRepository
     * @param AlphabetRepository          $alphabetRepository
     * @param ContentCategoryRepository   $contentCategoryRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        WallCarrierRepository $wallCarrierRepository,
        ItemCarrierRepository $itemCarrierRepository,
        MonumentCarrierRepository $monumentCarrierRepository,
        WritingTypeRepository $writingTypeRepository,
        MaterialRepository $materialRepository,
        WritingMethodRepository $writingMethodRepository,
        PreservationStateRepository $preservationStateRepository,
        AlphabetRepository $alphabetRepository,
        ContentCategoryRepository $contentCategoryRepository
    ) {
        $this->entityManager = $entityManager;
        $this->wallCarrierRepository = $wallCarrierRepository;
        $this->itemCarrierRepository = $itemCarrierRepository;
        $this->monumentCarrierRepository = $monumentCarrierRepository;
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

        while ($this->readRow($sheet, $rowIndex)) {
            ++$rowIndex;
        }
    }

    /**
     * @param Worksheet $sheet
     * @param int       $rowIndex
     *
     * @return bool
     */
    private function readRow(Worksheet $sheet, int $rowIndex): bool
    {
        $firstCellInTheRowValue = $sheet->getCellByColumnAndRow(1, $rowIndex)->getFormattedValue();

        if (self::NEW_ROW_ID === $firstCellInTheRowValue) {
            $inscription = new Inscription();

            foreach ($this->getSchema() as $rawColumnIndex => $cellValueHandler) {
                $columnIndex = $rawColumnIndex + 1;

                $columnValue = $sheet->getCellByColumnAndRow($columnIndex, $rowIndex)->getFormattedValue();

                $cellValueHandler($inscription, $columnValue);
            }

            $this->entityManager->persist($inscription);

            $this->entityManager->flush();
        }

        return '' !== $firstCellInTheRowValue;
    }

    /**
     * @return callable[]
     */
    private function getSchema(): array
    {
        return [
            function (Inscription $inscription, string $id): void {
            },
            function (Inscription $inscription, string $formattedCarrier): void {
                $carrierDiscriminators = [
                    XlsxExporter::WALL_CARRIER_DISCRIMINATOR,
                    XlsxExporter::ITEM_CARRIER_DISCRIMINATOR,
                    XlsxExporter::MONUMENT_CARRIER_DISCRIMINATOR,
                ];

                $regex = '/('.implode('|', $carrierDiscriminators).')\: ([^;]+)(?:; ([^;]+)(?:; ([^;]+))?)?/';

                if (1 === preg_match($regex, $formattedCarrier, $matches)) {
                    $carrierDiscriminator = $matches[1];
                    $carrierName = $matches[2];

                    $buildingTypeNameMatchIndex = 3;
                    $buildingNameMatchIndex = 4;

                    switch ($carrierDiscriminator) {
                        case XlsxExporter::WALL_CARRIER_DISCRIMINATOR:
                            if (\count($matches) <= $buildingTypeNameMatchIndex) {
                                $carrier = $this->wallCarrierRepository->findOneByNameOrCreate($carrierName);
                            } else {
                                $buildingTypeName = $matches[$buildingTypeNameMatchIndex];
                                $buildingName = \count($matches) <= $buildingNameMatchIndex
                                    ? null
                                    : $matches[$buildingNameMatchIndex];

                                $carrier = $this->wallCarrierRepository->findOneOrCreate(
                                    $carrierName,
                                    $buildingTypeName,
                                    $buildingName
                                );
                            }

                            break;
                        case XlsxExporter::ITEM_CARRIER_DISCRIMINATOR:
                            $carrier = $this->itemCarrierRepository->findOneByNameOrCreate($carrierName);

                            break;
                        case XlsxExporter::MONUMENT_CARRIER_DISCRIMINATOR:
                            $carrier = $this->monumentCarrierRepository->findOneByNameOrCreate($carrierName);

                            break;
                        default:
                            throw new InvalidArgumentException(
                                sprintf(
                                    'Invalid carrier discriminator "%s"',
                                    $carrierDiscriminator
                                )
                            );
                    }

                    $inscription->setCarrier($carrier);
                } else {
                    throw new InvalidArgumentException(sprintf('Invalid formatted carrier "%s"', $formattedCarrier));
                }
            },
            function (Inscription $inscription, string $formattedIsInSitu): void {
                if (!\in_array($formattedIsInSitu, [XlsxExporter::IS_IN_SITU_YES, XlsxExporter::IS_IN_SITU_NO], true)) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Invalid value "%s" for column "is in situ". Valid values are: "%s" and "%s"',
                            $formattedIsInSitu,
                            XlsxExporter::IS_IN_SITU_YES,
                            XlsxExporter::IS_IN_SITU_NO
                        )
                    );
                }

                $inscription->setIsInSitu(
                    XlsxExporter::IS_IN_SITU_YES === $formattedIsInSitu
                );
            },
            function (Inscription $inscription, string $formattedPlaceOnCarrier): void {
                $inscription->setPlaceOnCarrier(
                    $this->nullIfEmpty($formattedPlaceOnCarrier)
                );
            },
            function (Inscription $inscription, string $formattedWritingType): void {
                $inscription->setWritingType(
                    $this->nullOrNamedEntity($this->writingTypeRepository, $formattedWritingType)
                );
            },
            function (Inscription $inscription, string $formattedMaterials): void {
                $formattedMaterialsParts = explode(XlsxExporter::MATERIAL_SEPARATOR, $formattedMaterials);

                foreach ($formattedMaterialsParts as $formattedMaterial) {
                    $inscription->addMaterial($this->materialRepository->findOneByNameOrCreate($formattedMaterial));
                }
            },
            function (Inscription $inscription, string $formattedWritingMethod): void {
                $inscription->setWritingMethod(
                    $this->nullOrNamedEntity($this->writingMethodRepository, $formattedWritingMethod)
                );
            },
            function (Inscription $inscription, string $formattedPreservationState): void {
                $inscription->setPreservationState(
                    $this->nullOrNamedEntity($this->preservationStateRepository, $formattedPreservationState)
                );
            },
            function (Inscription $inscription, string $formattedAlphabet): void {
                $inscription->setAlphabet(
                    $this->nullOrNamedEntity($this->alphabetRepository, $formattedAlphabet)
                );
            },
            function (Inscription $inscription, string $formattedText): void {
                $inscription->setText(
                    $this->nullIfEmpty($formattedText)
                );
            },
            function (Inscription $inscription, string $formattedNewText): void {
                $inscription->setNewText(
                    $this->nullIfEmpty($formattedNewText)
                );
            },
            function (Inscription $inscription, string $formattedTransliteration): void {
                $inscription->setTransliteration(
                    $this->nullIfEmpty($formattedTransliteration)
                );
            },
            function (Inscription $inscription, string $formattedTranslation): void {
                $inscription->setTranslation(
                    $this->nullIfEmpty($formattedTranslation)
                );
            },
            function (Inscription $inscription, string $formattedContentCategory): void {
                $inscription->setContentCategory(
                    $this->nullOrNamedEntity($this->contentCategoryRepository, $formattedContentCategory)
                );
            },
            function (Inscription $inscription, string $formattedDateInText): void {
                $inscription->setDateInText(
                    $this->nullIfEmpty($formattedDateInText)
                );
            },
            function (Inscription $inscription, string $formattedCommentOnDate): void {
                $inscription->setCommentOnDate(
                    $this->nullIfEmpty($formattedCommentOnDate)
                );
            },
            function (Inscription $inscription, string $formattedCommentOnText): void {
                $inscription->setCommentOnText(
                    $this->nullIfEmpty($formattedCommentOnText)
                );
            },
        ];
    }

    /**
     * @param string $string
     *
     * @return string|null
     */
    private function nullIfEmpty(string $string): ?string
    {
        if ('' === $string) {
            return null;
        }

        return $string;
    }

    /**
     * @param NamedEntityRepository $namedEntityRepository
     * @param string                $name
     *
     * @throws ORMException
     *
     * @return NamedEntityInterface|null
     */
    private function nullOrNamedEntity(
        NamedEntityRepository $namedEntityRepository,
        string $name
    ): ?NamedEntityInterface {
        if ('' === $name) {
            return null;
        }

        return $namedEntityRepository->findOneByNameOrCreate($name);
    }
}
