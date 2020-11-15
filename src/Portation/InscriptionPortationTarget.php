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

namespace App\Portation;

use App\Helper\StringHelper;
use App\Persistence\Entity\Epigraphy\Carrier;
use App\Persistence\Entity\Epigraphy\Inscription;
use App\Persistence\Entity\Epigraphy\Interpretation;
use App\Persistence\Entity\Epigraphy\NamedEntityInterface;
use App\Persistence\Entity\Epigraphy\ZeroRow;
use App\Persistence\Repository\Epigraphy\AlphabetRepository;
use App\Persistence\Repository\Epigraphy\CarrierCategoryRepository;
use App\Persistence\Repository\Epigraphy\CarrierRepository;
use App\Persistence\Repository\Epigraphy\CarrierTypeRepository;
use App\Persistence\Repository\Epigraphy\ContentCategoryRepository;
use App\Persistence\Repository\Epigraphy\InscriptionRepository;
use App\Persistence\Repository\Epigraphy\InterpretationRepository;
use App\Persistence\Repository\Epigraphy\MaterialRepository;
use App\Persistence\Repository\Epigraphy\NamedEntityRepository;
use App\Persistence\Repository\Epigraphy\PreservationStateRepository;
use App\Persistence\Repository\Epigraphy\WritingMethodRepository;
use App\Persistence\Repository\Epigraphy\WritingTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use LogicException;
use Vyfony\Bundle\PortationBundle\Formatter\Bool\BoolFormatterInterface;
use Vyfony\Bundle\PortationBundle\RowType\EntityRow;
use Vyfony\Bundle\PortationBundle\RowType\RowTypeInterface;
use Vyfony\Bundle\PortationBundle\Target\PortationTargetInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class InscriptionPortationTarget implements PortationTargetInterface
{
    private const NEW_INSCRIPTION_ROW_KEY = '+';

    private const NEW_ZERO_ROW_KEY = '+z';

    private const NEW_INTERPRETATION_ROW_KEY = '++';

    private const MANY_TO_MANY_SEPARATOR = ', ';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var BoolFormatterInterface
     */
    private $boolFormatter;

    /**
     * @var CarrierTypeRepository
     */
    private $carrierTypeRepository;

    /**
     * @var CarrierCategoryRepository
     */
    private $carrierCategoryRepository;

    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * @var InscriptionRepository
     */
    private $inscriptionRepository;

    /**
     * @var InterpretationRepository
     */
    private $interpretationRepository;

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

    public function __construct(
        EntityManagerInterface $entityManager,
        BoolFormatterInterface $boolFormatter,
        CarrierTypeRepository $carrierTypeRepository,
        CarrierCategoryRepository $carrierCategoryRepository,
        CarrierRepository $carrierRepository,
        InscriptionRepository $inscriptionRepository,
        InterpretationRepository $interpretationRepository,
        WritingTypeRepository $writingTypeRepository,
        MaterialRepository $materialRepository,
        WritingMethodRepository $writingMethodRepository,
        PreservationStateRepository $preservationStateRepository,
        AlphabetRepository $alphabetRepository,
        ContentCategoryRepository $contentCategoryRepository
    ) {
        $this->entityManager = $entityManager;
        $this->boolFormatter = $boolFormatter;
        $this->carrierTypeRepository = $carrierTypeRepository;
        $this->carrierCategoryRepository = $carrierCategoryRepository;
        $this->carrierRepository = $carrierRepository;
        $this->inscriptionRepository = $inscriptionRepository;
        $this->interpretationRepository = $interpretationRepository;
        $this->writingTypeRepository = $writingTypeRepository;
        $this->materialRepository = $materialRepository;
        $this->writingMethodRepository = $writingMethodRepository;
        $this->preservationStateRepository = $preservationStateRepository;
        $this->alphabetRepository = $alphabetRepository;
        $this->contentCategoryRepository = $contentCategoryRepository;
    }

    /**
     * @return string[]
     */
    public function getCellValues(object $entity): array
    {
        $formatNamedEntity = function (NamedEntityInterface $namedEntity = null): ?string {
            return null === $namedEntity ? null : $namedEntity->getName();
        };

        $combineValueWithReferences = function (?string $value, Collection $interpretations): string {
            $formattedValues = [];

            if (null !== $value) {
                $formattedValues[] = $value;
            }

            foreach ($interpretations as $interpretation) {
                $formattedValues[] = sprintf('[[%s]]', $interpretation->getSource());
            }

            return implode(', ', $formattedValues);
        };

        switch (true) {
            case $entity instanceof Inscription:
                $carrier = $entity->getCarrier();

                return [
                    'id' => (string) $entity->getId(),
                    'inscription.conventionalDate' => $entity->getConventionalDate(),
                    'inscription.carrier.types' => StringHelper::nullIfEmpty(
                        implode(
                            self::MANY_TO_MANY_SEPARATOR,
                            $carrier->getTypes()->map($formatNamedEntity)->toArray()
                        )
                    ),
                    'inscription.carrier.categories' => StringHelper::nullIfEmpty(
                        implode(
                            self::MANY_TO_MANY_SEPARATOR,
                            $carrier->getCategories()->map($formatNamedEntity)->toArray()
                        )
                    ),
                    'inscription.carrier.origin1' => $carrier->getOrigin1(),
                    'inscription.carrier.origin2' => $carrier->getOrigin2(),
                    'inscription.carrier.findCircumstances' => $carrier->getFindCircumstances(),
                    'inscription.carrier.characteristics' => $carrier->getCharacteristics(),
                    'inscription.carrier.individualName' => $carrier->getIndividualName(),
                    'inscription.carrier.storagePlace' => $carrier->getStoragePlace(),
                    'inscription.carrier.inventoryNumber' => $carrier->getInventoryNumber(),
                    'inscription.carrier.isInSitu' => $this->boolFormatter->format($carrier->getIsInSitu()),
                ];
            case $entity instanceof ZeroRow:
                return [
                    'id' => sprintf(
                        '%d.%d',
                        $entity->getInscription()->getId(),
                        $entity->getId()
                    ),
                    'interpretation.source' => '',
                    'interpretation.comment' => '',
                    'interpretation.pageNumbersInSource' => '',
                    'interpretation.numberInSource' => '',
                    'interpretation.placeOnCarrier' => $combineValueWithReferences(
                        $entity->getPlaceOnCarrier(),
                        $entity->getPlaceOnCarrierReferences()
                    ),
                    'interpretation.writingTypes' => $combineValueWithReferences(
                        StringHelper::nullIfEmpty(
                            implode(
                                self::MANY_TO_MANY_SEPARATOR,
                                $entity->getWritingTypes()->map($formatNamedEntity)->toArray()
                            )
                        ),
                        $entity->getWritingTypesReferences()
                    ),
                    'interpretation.writingMethods' => $combineValueWithReferences(
                        StringHelper::nullIfEmpty(
                            implode(
                                self::MANY_TO_MANY_SEPARATOR,
                                $entity->getWritingMethods()->map($formatNamedEntity)->toArray()
                            )
                        ),
                        $entity->getWritingMethodsReferences()
                    ),
                    'interpretation.preservationState' => $combineValueWithReferences(
                        StringHelper::nullIfEmpty(
                            implode(
                                self::MANY_TO_MANY_SEPARATOR,
                                $entity->getPreservationStates()->map($formatNamedEntity)->toArray()
                            )
                        ),
                        $entity->getPreservationStatesReferences()
                    ),
                    'interpretation.materials' => $combineValueWithReferences(
                        StringHelper::nullIfEmpty(
                            implode(
                                self::MANY_TO_MANY_SEPARATOR,
                                $entity->getMaterials()->map($formatNamedEntity)->toArray()
                            )
                        ),
                        $entity->getMaterialsReferences()
                    ),
                    'interpretation.alphabets' => $combineValueWithReferences(
                        StringHelper::nullIfEmpty(
                            implode(
                                self::MANY_TO_MANY_SEPARATOR,
                                $entity->getAlphabets()->map($formatNamedEntity)->toArray()
                            )
                        ),
                        $entity->getAlphabetsReferences()
                    ),
                    'interpretation.text' => $combineValueWithReferences(
                        $entity->getText(),
                        $entity->getTextReferences()
                    ),
                    'interpretation.textImageFileNames' => $combineValueWithReferences(
                        $entity->getTextImageFileNames(),
                        $entity->getTextImageFileNamesReferences()
                    ),
                    'interpretation.transliteration' => $combineValueWithReferences(
                        $entity->getTransliteration(),
                        $entity->getTransliterationReferences()
                    ),
                    'interpretation.translation' => $combineValueWithReferences(
                        $entity->getTranslation(),
                        $entity->getTranslationReferences()
                    ),
                    'interpretation.contentCategories' => $combineValueWithReferences(
                        StringHelper::nullIfEmpty(
                            implode(
                                self::MANY_TO_MANY_SEPARATOR,
                                $entity->getContentCategories()->map($formatNamedEntity)->toArray()
                            )
                        ),
                        $entity->getContentCategoriesReferences()
                    ),
                    'interpretation.content' => $combineValueWithReferences(
                        $entity->getContent(),
                        $entity->getContentReferences()
                    ),
                    'interpretation.dateInText' => $combineValueWithReferences(
                        $entity->getDateInText(),
                        $entity->getDateInTextReferences()
                    ),
                    'interpretation.stratigraphicalDate' => $combineValueWithReferences(
                        $entity->getStratigraphicalDate(),
                        $entity->getStratigraphicalDateReferences()
                    ),
                    'interpretation.nonStratigraphicalDate' => $combineValueWithReferences(
                        $entity->getNonStratigraphicalDate(),
                        $entity->getNonStratigraphicalDateReferences()
                    ),
                    'interpretation.historicalDate' => $combineValueWithReferences(
                        $entity->getHistoricalDate(),
                        $entity->getHistoricalDateReferences()
                    ),
                    'interpretation.photoFileNames' => $combineValueWithReferences(
                        $entity->getPhotoFileNames(),
                        $entity->getPhotoFileNamesReferences()
                    ),
                    'interpretation.sketchFileNames' => $combineValueWithReferences(
                        $entity->getSketchFileNames(),
                        $entity->getSketchFileNamesReferences()
                    ),
                ];
            case $entity instanceof Interpretation:
                return [
                    'id' => sprintf(
                        '%d.%d',
                        $entity->getInscription()->getId(),
                        $entity->getId()
                    ),
                    'interpretation.source' => $entity->getSource(),
                    'interpretation.comment' => $entity->getComment(),
                    'interpretation.pageNumbersInSource' => $entity->getPageNumbersInSource(),
                    'interpretation.numberInSource' => $entity->getNumberInSource(),
                    'interpretation.placeOnCarrier' => $entity->getPlaceOnCarrier(),
                    'interpretation.writingTypes' => implode(
                        self::MANY_TO_MANY_SEPARATOR,
                        $entity->getWritingTypes()->map($formatNamedEntity)->toArray()
                    ),
                    'interpretation.writingMethods' => implode(
                        self::MANY_TO_MANY_SEPARATOR,
                        $entity->getWritingMethods()->map($formatNamedEntity)->toArray()
                    ),
                    'interpretation.preservationStates' => implode(
                        self::MANY_TO_MANY_SEPARATOR,
                        $entity->getPreservationStates()->map($formatNamedEntity)->toArray()
                    ),
                    'interpretation.materials' => implode(
                        self::MANY_TO_MANY_SEPARATOR,
                        $entity->getMaterials()->map($formatNamedEntity)->toArray()
                    ),
                    'interpretation.alphabets' => implode(
                        self::MANY_TO_MANY_SEPARATOR,
                        $entity->getAlphabets()->map($formatNamedEntity)->toArray()
                    ),
                    'interpretation.text' => $entity->getText(),
                    'interpretation.textImageFileNames' => $entity->getTextImageFileNames(),
                    'interpretation.transliteration' => $entity->getTransliteration(),
                    'interpretation.translation' => $entity->getTranslation(),
                    'interpretation.contentCategories' => implode(
                        self::MANY_TO_MANY_SEPARATOR,
                        $entity->getContentCategories()->map($formatNamedEntity)->toArray()
                    ),
                    'interpretation.content' => $entity->getContent(),
                    'interpretation.dateInText' => $entity->getDateInText(),
                    'interpretation.stratigraphicalDate' => $entity->getStratigraphicalDate(),
                    'interpretation.nonStratigraphicalDate' => $entity->getNonStratigraphicalDate(),
                    'interpretation.historicalDate' => $entity->getHistoricalDate(),
                    'interpretation.photoFileNames' => $entity->getPhotoFileNames(),
                    'interpretation.sketchFileNames' => $entity->getSketchFileNames(),
                ];
            default:
                throw $this->createInvalidEntityTypeException($entity);
        }
    }

    /**
     * @return callable[]
     */
    public function getCellValueHandlers(RowTypeInterface $rowType): array
    {
        $getOrCreateCarrier = function (Inscription $inscription): Carrier {
            $carrier = $inscription->getCarrier();

            if (null === $carrier) {
                $carrier = new Carrier();

                $inscription->setCarrier($carrier);
            }

            return $inscription->getCarrier();
        };

        switch ($rowType->getNewRowKey()) {
            case self::NEW_INSCRIPTION_ROW_KEY:
                return [
                    'inscription.conventionalDate' => function (
                        Inscription $inscription,
                        string $formattedConventionalDate
                    ): void {
                        $inscription->setConventionalDate($formattedConventionalDate);
                    },
                    'inscription.carrier.types' => function (
                        Inscription $inscription,
                        string $formattedCarrierType
                    ) use ($getOrCreateCarrier): void {
                        $getOrCreateCarrier($inscription)->setTypes(
                            $this->carrierTypeRepository->findOneByNameOrCreate($formattedCarrierType)
                        );
                    },
                    'inscription.carrier.categories' => function (
                        Inscription $inscription,
                        string $formattedCarrierCategory
                    ) use ($getOrCreateCarrier): void {
                        $getOrCreateCarrier($inscription)->setCategories(
                            $this->carrierCategoryRepository->findOneByNameOrCreate($formattedCarrierCategory)
                        );
                    },
                    'inscription.carrier.origin1' => function (
                        Inscription $inscription,
                        string $formattedOrigin1
                    ) use ($getOrCreateCarrier): void {
                        $getOrCreateCarrier($inscription)->setOrigin1(
                            StringHelper::nullIfEmpty($formattedOrigin1)
                        );
                    },
                    'inscription.carrier.origin2' => function (
                        Inscription $inscription,
                        string $formattedOrigin2
                    ) use ($getOrCreateCarrier): void {
                        $getOrCreateCarrier($inscription)->setOrigin2(
                            StringHelper::nullIfEmpty($formattedOrigin2)
                        );
                    },
                    'inscription.carrier.findCircumstances' => function (
                        Inscription $inscription,
                        string $formattedFindCircumstances
                    ) use ($getOrCreateCarrier): void {
                        $getOrCreateCarrier($inscription)->setFindCircumstances(
                            StringHelper::nullIfEmpty($formattedFindCircumstances)
                        );
                    },
                    'inscription.carrier.characteristics' => function (
                        Inscription $inscription,
                        string $formattedCharacteristics
                    ) use ($getOrCreateCarrier): void {
                        $getOrCreateCarrier($inscription)->setCharacteristics(
                            StringHelper::nullIfEmpty($formattedCharacteristics)
                        );
                    },
                    'inscription.carrier.individualName' => function (
                        Inscription $inscription,
                        string $formattedIndividualName
                    ) use ($getOrCreateCarrier): void {
                        $getOrCreateCarrier($inscription)->setIndividualName(
                            StringHelper::nullIfEmpty($formattedIndividualName)
                        );
                    },
                    'inscription.carrier.storagePlace' => function (
                        Inscription $inscription,
                        string $formattedStoragePlace
                    ) use ($getOrCreateCarrier): void {
                        $getOrCreateCarrier($inscription)->setStoragePlace(
                            StringHelper::nullIfEmpty($formattedStoragePlace)
                        );
                    },
                    'inscription.carrier.inventoryNumber' => function (
                        Inscription $inscription,
                        string $formattedInventoryNumber
                    ) use ($getOrCreateCarrier): void {
                        $getOrCreateCarrier($inscription)->setInventoryNumber(
                            StringHelper::nullIfEmpty($formattedInventoryNumber)
                        );
                    },
                    'inscription.carrier.isInSitu' => function (
                        Inscription $inscription,
                        string $formattedIsInSitu
                    ) use ($getOrCreateCarrier): void {
                        $getOrCreateCarrier($inscription)->setIsInSitu(
                            $this->boolFormatter->parse(StringHelper::nullIfEmpty($formattedIsInSitu))
                        );
                    },
                ];
            case self::NEW_ZERO_ROW_KEY:
                return [
                    'interpretation.source' => function (
                        ZeroRow $zeroRow,
                        string $formattedSource
                    ): void {
                    },
                    'interpretation.comment' => function (
                        ZeroRow $zeroRow,
                        string $formattedComment
                    ): void {
                    },
                    'interpretation.pageNumbersInSource' => function (
                        ZeroRow $zeroRow,
                        string $formattedPageNumbersInSource
                    ): void {
                    },
                    'interpretation.numberInSource' => function (
                        ZeroRow $zeroRow,
                        string $formattedNumberInSource
                    ): void {
                    },
                    'interpretation.placeOnCarrier' => function (
                        ZeroRow $zeroRow,
                        string $formattedPlaceOnCarrier
                    ): void {
                        $zeroRow->setPlaceOnCarrier(
                            StringHelper::nullIfEmpty($formattedPlaceOnCarrier)
                        );
                    },
                    'interpretation.writingTypes' => function (
                        ZeroRow $zeroRow,
                        string $formattedWritingTypes
                    ): void {
                        $zeroRow->setWritingTypes(
                            $this->parseAsCollectionOfNamedEntities(
                                $this->writingTypeRepository,
                                $formattedWritingTypes
                            )
                        );
                    },
                    'interpretation.writingMethods' => function (
                        ZeroRow $zeroRow,
                        string $formattedWritingMethods
                    ): void {
                        $zeroRow->setWritingMethods(
                            $this->parseAsCollectionOfNamedEntities(
                                $this->writingMethodRepository,
                                $formattedWritingMethods
                            )
                        );
                    },
                    'interpretation.preservationState' => function (
                        ZeroRow $zeroRow,
                        string $formattedPreservationStates
                    ): void {
                        $zeroRow->setPreservationStates(
                            $this->parseAsCollectionOfNamedEntities(
                                $this->preservationStateRepository,
                                $formattedPreservationStates
                            )
                        );
                    },
                    'interpretation.materials' => function (
                        ZeroRow $zeroRow,
                        string $formattedMaterials
                    ): void {
                        $zeroRow->setMaterials(
                            $this->parseAsCollectionOfNamedEntities(
                                $this->materialRepository,
                                $formattedMaterials
                            )
                        );
                    },
                    'interpretation.alphabets' => function (
                        ZeroRow $zeroRow,
                        string $formattedAlphabets
                    ): void {
                        $zeroRow->setAlphabets(
                            $this->parseAsCollectionOfNamedEntities(
                                $this->alphabetRepository,
                                $formattedAlphabets
                            )
                        );
                    },
                    'interpretation.text' => function (
                        ZeroRow $zeroRow,
                        string $formattedText
                    ): void {
                        $zeroRow->setText(
                            StringHelper::nullIfEmpty($formattedText)
                        );
                    },
                    'interpretation.textImageFileNames' => function (
                        ZeroRow $zeroRow,
                        string $formattedTextImageFileName
                    ): void {
                        $zeroRow->setTextImageFileNames(
                            StringHelper::nullIfEmpty($formattedTextImageFileName)
                        );
                    },
                    'interpretation.transliteration' => function (
                        ZeroRow $zeroRow,
                        string $formattedTransliteration
                    ): void {
                        $zeroRow->setTransliteration(
                            StringHelper::nullIfEmpty($formattedTransliteration)
                        );
                    },
                    'interpretation.translation' => function (
                        ZeroRow $zeroRow,
                        string $formattedTranslation
                    ): void {
                        $zeroRow->setTranslation(
                            StringHelper::nullIfEmpty($formattedTranslation)
                        );
                    },
                    'interpretation.contentCategories' => function (
                        ZeroRow $zeroRow,
                        string $formattedContentCategories
                    ): void {
                        $zeroRow->setContentCategories(
                            $this->parseAsCollectionOfNamedEntities(
                                $this->contentCategoryRepository,
                                $formattedContentCategories
                            )
                        );
                    },
                    'interpretation.content' => function (
                        ZeroRow $zeroRow,
                        string $formattedContent
                    ): void {
                        $zeroRow->setContent(
                            StringHelper::nullIfEmpty($formattedContent)
                        );
                    },
                    'interpretation.dateInText' => function (
                        ZeroRow $zeroRow,
                        string $formattedDateInText
                    ): void {
                        $zeroRow->setDateInText(
                            StringHelper::nullIfEmpty($formattedDateInText)
                        );
                    },
                    'interpretation.stratigraphicalDate' => function (
                        ZeroRow $zeroRow,
                        string $formattedStratigraphicalDate
                    ): void {
                        $zeroRow->setStratigraphicalDate(
                            StringHelper::nullIfEmpty($formattedStratigraphicalDate)
                        );
                    },
                    'interpretation.nonStratigraphicalDate' => function (
                        ZeroRow $zeroRow,
                        string $formattedNonStratigraphicalDate
                    ): void {
                        $zeroRow->setNonStratigraphicalDate(
                            StringHelper::nullIfEmpty($formattedNonStratigraphicalDate)
                        );
                    },
                    'interpretation.historicalDate' => function (
                        ZeroRow $zeroRow,
                        string $formattedHistoricalDate
                    ): void {
                        $zeroRow->setHistoricalDate(
                            StringHelper::nullIfEmpty($formattedHistoricalDate)
                        );
                    },
                    'interpretation.photoFileNames' => function (
                        ZeroRow $zeroRow,
                        string $formattedPhotoFileName
                    ): void {
                        $zeroRow->setPhotoFileNames(
                            StringHelper::nullIfEmpty($formattedPhotoFileName)
                        );
                    },
                    'interpretation.sketchFileNames' => function (
                        ZeroRow $zeroRow,
                        string $formattedSketchFileName
                    ): void {
                        $zeroRow->setSketchFileNames(
                            StringHelper::nullIfEmpty($formattedSketchFileName)
                        );
                    },
                ];
            case self::NEW_INTERPRETATION_ROW_KEY:
                return [
                    'interpretation.source' => function (
                        Interpretation $interpretation,
                        string $formattedSource
                    ): void {
                        $interpretation->setSource($formattedSource);
                    },
                    'interpretation.comment' => function (
                        Interpretation $interpretation,
                        string $formattedComment
                    ): void {
                        $interpretation->setComment(
                            StringHelper::nullIfEmpty($formattedComment)
                        );
                    },
                    'interpretation.pageNumbersInSource' => function (
                        Interpretation $interpretation,
                        string $formattedPageNumbersInSource
                    ): void {
                        $interpretation->setPageNumbersInSource(
                            StringHelper::nullIfEmpty($formattedPageNumbersInSource)
                        );
                    },
                    'interpretation.numberInSource' => function (
                        Interpretation $interpretation,
                        string $formattedNumberInSource
                    ): void {
                        $interpretation->setNumberInSource(
                            StringHelper::nullIfEmpty($formattedNumberInSource)
                        );
                    },
                    'interpretation.placeOnCarrier' => function (
                        Interpretation $interpretation,
                        string $formattedPlaceOnCarrier
                    ): void {
                        $interpretation->setPlaceOnCarrier(
                            StringHelper::nullIfEmpty($formattedPlaceOnCarrier)
                        );
                    },
                    'interpretation.writingTypes' => function (
                        Interpretation $interpretation,
                        string $formattedWritingTypes
                    ): void {
                        $interpretation->setWritingTypes(
                            $this->parseAsCollectionOfNamedEntities(
                                $this->writingTypeRepository,
                                $formattedWritingTypes
                            )
                        );
                    },
                    'interpretation.writingMethods' => function (
                        Interpretation $interpretation,
                        string $formattedWritingMethods
                    ): void {
                        $interpretation->setWritingMethods(
                            $this->parseAsCollectionOfNamedEntities(
                                $this->writingMethodRepository,
                                $formattedWritingMethods
                            )
                        );
                    },
                    'interpretation.preservationStates' => function (
                        Interpretation $interpretation,
                        string $formattedPreservationStates
                    ): void {
                        $interpretation->setPreservationStates(
                            $this->parseAsCollectionOfNamedEntities(
                                $this->preservationStateRepository,
                                $formattedPreservationStates
                            )
                        );
                    },
                    'interpretation.materials' => function (
                        Interpretation $interpretation,
                        string $formattedMaterials
                    ): void {
                        $interpretation->setMaterials(
                            $this->parseAsCollectionOfNamedEntities(
                                $this->materialRepository,
                                $formattedMaterials
                            )
                        );
                    },
                    'interpretation.alphabets' => function (
                        Interpretation $interpretation,
                        string $formattedAlphabets
                    ): void {
                        $interpretation->setAlphabets(
                            $this->parseAsCollectionOfNamedEntities(
                                $this->alphabetRepository,
                                $formattedAlphabets
                            )
                        );
                    },
                    'interpretation.text' => function (
                        Interpretation $interpretation,
                        string $formattedText
                    ): void {
                        $interpretation->setText(
                            StringHelper::nullIfEmpty($formattedText)
                        );
                    },
                    'interpretation.textImageFileNames' => function (
                        Interpretation $interpretation,
                        string $formattedTextImageFileName
                    ): void {
                        $interpretation->setTextImageFileNames(
                            StringHelper::nullIfEmpty($formattedTextImageFileName)
                        );
                    },
                    'interpretation.transliteration' => function (
                        Interpretation $interpretation,
                        string $formattedTransliteration
                    ): void {
                        $interpretation->setTransliteration(
                            StringHelper::nullIfEmpty($formattedTransliteration)
                        );
                    },
                    'interpretation.translation' => function (
                        Interpretation $interpretation,
                        string $formattedTranslation
                    ): void {
                        $interpretation->setTranslation(
                            StringHelper::nullIfEmpty($formattedTranslation)
                        );
                    },
                    'interpretation.contentCategories' => function (
                        Interpretation $interpretation,
                        string $formattedContentCategories
                    ): void {
                        $interpretation->setContentCategories(
                            $this->parseAsCollectionOfNamedEntities(
                                $this->contentCategoryRepository,
                                $formattedContentCategories
                            )
                        );
                    },
                    'interpretation.content' => function (
                        Interpretation $interpretation,
                        string $formattedContent
                    ): void {
                        $interpretation->setContent(
                            StringHelper::nullIfEmpty($formattedContent)
                        );
                    },
                    'interpretation.dateInText' => function (
                        Interpretation $interpretation,
                        string $formattedDateInText
                    ): void {
                        $interpretation->setDateInText(
                            StringHelper::nullIfEmpty($formattedDateInText)
                        );
                    },
                    'interpretation.stratigraphicalDate' => function (
                        Interpretation $interpretation,
                        string $formattedStratigraphicalDate
                    ): void {
                        $interpretation->setStratigraphicalDate(
                            StringHelper::nullIfEmpty($formattedStratigraphicalDate)
                        );
                    },
                    'interpretation.nonStratigraphicalDate' => function (
                        Interpretation $interpretation,
                        string $formattedNonStratigraphicalDate
                    ): void {
                        $interpretation->setNonStratigraphicalDate(
                            StringHelper::nullIfEmpty($formattedNonStratigraphicalDate)
                        );
                    },
                    'interpretation.historicalDate' => function (
                        Interpretation $interpretation,
                        string $formattedHistoricalDate
                    ): void {
                        $interpretation->setHistoricalDate(
                            StringHelper::nullIfEmpty($formattedHistoricalDate)
                        );
                    },
                    'interpretation.photoFileNames' => function (
                        Interpretation $interpretation,
                        string $formattedPhotoFileName
                    ): void {
                        $interpretation->setPhotoFileNames(
                            StringHelper::nullIfEmpty($formattedPhotoFileName)
                        );
                    },
                    'interpretation.sketchFileNames' => function (
                        Interpretation $interpretation,
                        string $formattedSketchFileName
                    ): void {
                        $interpretation->setSketchFileNames(
                            StringHelper::nullIfEmpty($formattedSketchFileName)
                        );
                    },
                ];
            default:
                throw $this->createInvalidNewRowKeyException($rowType->getNewRowKey());
        }
    }

    public function createEntity(RowTypeInterface $rowType): object
    {
        switch ($rowType->getNewRowKey()) {
            case self::NEW_INSCRIPTION_ROW_KEY:
                return new Inscription();
            case self::NEW_ZERO_ROW_KEY:
                return new ZeroRow();
            case self::NEW_INTERPRETATION_ROW_KEY:
                return new Interpretation();
            default:
                throw $this->createInvalidNewRowKeyException($rowType->getNewRowKey());
        }
    }

    public function setNestedEntity(string $entityRowKey, object $entity, object $nestedEntity): void
    {
        $entityIsInscription = $entity instanceof Inscription;

        $nestedEntityIsInterpretation = $nestedEntity instanceof Interpretation;

        $nestedEntityIsZeroRow = $nestedEntity instanceof ZeroRow;

        switch (true) {
            case $entityIsInscription && $nestedEntityIsInterpretation:
                $entity->addInterpretation($nestedEntity);

                break;
            case $entityIsInscription && $nestedEntityIsZeroRow:
                $entity->setZeroRow($nestedEntity);

                break;
            default:
                throw $this->createInvalidNewRowKeyException($entityRowKey);
        }
    }

    /**
     * @return object[]
     */
    public function getEntities(): array
    {
        return $this->inscriptionRepository->findAll();
    }

    /**
     * @return object[]
     */
    public function getNestedEntities(RowTypeInterface $nestedRowType, object $entity): array
    {
        switch (true) {
            case $entity instanceof Inscription:
                switch ($nestedRowType->getNewRowKey()) {
                    case self::NEW_INTERPRETATION_ROW_KEY:
                        return $entity->getInterpretations()->toArray();
                    case self::NEW_ZERO_ROW_KEY:
                        return [$entity->getZeroRow()];
                    default:
                        throw $this->createInvalidNewRowKeyException($nestedRowType->getNewRowKey());
                }
                // no break
            case $entity instanceof Interpretation:
                throw $this->createUnexpectedEntityTypeException($entity, __METHOD__);
            default:
                throw $this->createInvalidEntityTypeException($entity);
        }
    }

    public function getRootRowType(): RowTypeInterface
    {
        return new EntityRow(
            self::NEW_INSCRIPTION_ROW_KEY,
            [
                new EntityRow(
                    self::NEW_ZERO_ROW_KEY,
                    []
                ),
                new EntityRow(
                    self::NEW_INTERPRETATION_ROW_KEY,
                    []
                ),
            ]
        );
    }

    /**
     * @return string[]
     */
    public function getSchema(): array
    {
        return [
            'id',
            'inscription.conventionalDate',
            'inscription.carrier.types',
            'inscription.carrier.categories',
            'inscription.carrier.origin1',
            'inscription.carrier.origin2',
            'inscription.carrier.findCircumstances',
            'inscription.carrier.characteristics',
            'inscription.carrier.individualName',
            'inscription.carrier.storagePlace',
            'inscription.carrier.inventoryNumber',
            'inscription.carrier.isInSitu',
            'interpretation.source',
            'interpretation.comment',
            'interpretation.pageNumbersInSource',
            'interpretation.numberInSource',
            'interpretation.placeOnCarrier',
            'interpretation.writingTypes',
            'interpretation.writingMethods',
            'interpretation.preservationStates',
            'interpretation.materials',
            'interpretation.alphabets',
            'interpretation.text',
            'interpretation.textImageFileNames',
            'interpretation.transliteration',
            'interpretation.translation',
            'interpretation.contentCategories',
            'interpretation.content',
            'interpretation.dateInText',
            'interpretation.stratigraphicalDate',
            'interpretation.nonStratigraphicalDate',
            'interpretation.historicalDate',
            'interpretation.photoFileNames',
            'interpretation.sketchFileNames',
        ];
    }

    public function save(object $entity): void
    {
        if (!$entity instanceof Inscription) {
            throw $this->createInvalidEntityTypeException($entity);
        }

        $this->normalizeCarrier($entity);

        $zeroRow = $entity->getZeroRow();

        $entity->setZeroRow(null);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $this->normalizeZeroRow($entity, $zeroRow);

        $entity->setZeroRow($zeroRow);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    private function normalizeCarrier(Inscription $inscription): void
    {
        $carrier = $inscription->getCarrier();

        $existingCarrier = $this->carrierRepository->findOneBy([
            'origin1' => $carrier->getOrigin1(),
            'origin2' => $carrier->getOrigin2(),
            'findCircumstances' => $carrier->getFindCircumstances(),
            'characteristics' => $carrier->getCharacteristics(),
            'individualName' => $carrier->getIndividualName(),
            'storagePlace' => $carrier->getStoragePlace(),
            'inventoryNumber' => $carrier->getInventoryNumber(),
            'isInSitu' => $carrier->getIsInSitu(),
        ]);

        if (null !== $existingCarrier) {
            $inscription->setCarrier($existingCarrier);
        }
    }

    private function normalizeZeroRow(Inscription $inscription, ZeroRow $zeroRow)
    {
        $this->tryUseStringValueAsReference(
            $inscription,
            $zeroRow->getPlaceOnCarrier(),
            static function () use ($zeroRow): void {
                $zeroRow->setPlaceOnCarrier(null);
            },
            static function (Interpretation $reference) use ($zeroRow): void {
                $zeroRow->getPlaceOnCarrierReferences()->add($reference);
            }
        );

        foreach ($zeroRow->getWritingTypes() as $key => $writingType) {
            $this->tryUseNamedEntityAsReference(
                $inscription,
                $writingType,
                static function () use ($zeroRow, $key): void {
                    $zeroRow->getWritingTypes()->remove($key);
                },
                static function (Interpretation $reference) use ($zeroRow): void {
                    $zeroRow->getWritingTypes()->add($reference);
                }
            );
        }

        foreach ($zeroRow->getWritingMethods() as $key => $writingMethod) {
            $this->tryUseNamedEntityAsReference(
                $inscription,
                $writingMethod,
                static function () use ($zeroRow, $key): void {
                    $zeroRow->getWritingMethods()->remove($key);
                },
                static function (Interpretation $reference) use ($zeroRow): void {
                    $zeroRow->getWritingMethods()->add($reference);
                }
            );
        }

        foreach ($zeroRow->getPreservationStates() as $key => $preservationState) {
            $this->tryUseNamedEntityAsReference(
                $inscription,
                $preservationState,
                static function () use ($zeroRow, $key): void {
                    $zeroRow->getPreservationStates()->remove($key);
                },
                static function (Interpretation $reference) use ($zeroRow): void {
                    $zeroRow->getPreservationStatesReferences()->add($reference);
                }
            );
        }

        foreach ($zeroRow->getMaterials() as $key => $material) {
            $this->tryUseNamedEntityAsReference(
                $inscription,
                $material,
                static function () use ($zeroRow, $key): void {
                    $zeroRow->getMaterials()->remove($key);
                },
                static function (Interpretation $reference) use ($zeroRow): void {
                    $zeroRow->getMaterialsReferences()->add($reference);
                }
            );
        }

        foreach ($zeroRow->getAlphabets() as $key => $alphabet) {
            $this->tryUseNamedEntityAsReference(
                $inscription,
                $alphabet,
                static function () use ($zeroRow, $key): void {
                    $zeroRow->getAlphabets()->remove($key);
                },
                static function (Interpretation $reference) use ($zeroRow): void {
                    $zeroRow->getAlphabets()->add($reference);
                }
            );
        }

        $this->tryUseStringValueAsReference(
            $inscription,
            $zeroRow->getText(),
            static function () use ($zeroRow): void {
                $zeroRow->setText(null);
            },
            static function (Interpretation $reference) use ($zeroRow): void {
                $zeroRow->getTextReferences()->add($reference);
            }
        );

        $this->tryUseStringValueAsReference(
            $inscription,
            $zeroRow->getTextImageFileNames(),
            static function () use ($zeroRow): void {
                $zeroRow->setTextImageFileNames(null);
            },
            static function (Interpretation $reference) use ($zeroRow): void {
                $zeroRow->getTextImageFileNamesReferences()->add($reference);
            }
        );

        $this->tryUseStringValueAsReference(
            $inscription,
            $zeroRow->getTransliteration(),
            static function () use ($zeroRow): void {
                $zeroRow->setTransliteration(null);
            },
            static function (Interpretation $reference) use ($zeroRow): void {
                $zeroRow->getTransliterationReferences()->add($reference);
            }
        );

        $this->tryUseStringValueAsReference(
            $inscription,
            $zeroRow->getTranslation(),
            static function () use ($zeroRow): void {
                $zeroRow->setTranslation(null);
            },
            static function (Interpretation $reference) use ($zeroRow): void {
                $zeroRow->getTranslationReferences()->add($reference);
            }
        );

        foreach ($zeroRow->getContentCategories() as $key => $contentCategory) {
            $this->tryUseNamedEntityAsReference(
                $inscription,
                $contentCategory,
                static function () use ($zeroRow, $key): void {
                    $zeroRow->getContentCategories()->remove($key);
                },
                static function (Interpretation $reference) use ($zeroRow): void {
                    $zeroRow->getContentCategories()->add($reference);
                }
            );
        }

        $this->tryUseStringValueAsReference(
            $inscription,
            $zeroRow->getContent(),
            static function () use ($zeroRow): void {
                $zeroRow->setContent(null);
            },
            static function (Interpretation $reference) use ($zeroRow): void {
                $zeroRow->getContentReferences()->add($reference);
            }
        );

        $this->tryUseStringValueAsReference(
            $inscription,
            $zeroRow->getDateInText(),
            static function () use ($zeroRow): void {
                $zeroRow->setDateInText(null);
            },
            static function (Interpretation $reference) use ($zeroRow): void {
                $zeroRow->getDateInTextReferences()->add($reference);
            }
        );

        $this->tryUseStringValueAsReference(
            $inscription,
            $zeroRow->getStratigraphicalDate(),
            static function () use ($zeroRow): void {
                $zeroRow->setStratigraphicalDate(null);
            },
            static function (Interpretation $reference) use ($zeroRow): void {
                $zeroRow->getStratigraphicalDateReferences()->add($reference);
            }
        );

        $this->tryUseStringValueAsReference(
            $inscription,
            $zeroRow->getNonStratigraphicalDate(),
            static function () use ($zeroRow): void {
                $zeroRow->setNonStratigraphicalDate(null);
            },
            static function (Interpretation $reference) use ($zeroRow): void {
                $zeroRow->getNonStratigraphicalDateReferences()->add($reference);
            }
        );

        $this->tryUseStringValueAsReference(
            $inscription,
            $zeroRow->getHistoricalDate(),
            static function () use ($zeroRow): void {
                $zeroRow->setHistoricalDate(null);
            },
            static function (Interpretation $reference) use ($zeroRow): void {
                $zeroRow->getHistoricalDateReferences()->add($reference);
            }
        );

        $this->tryUseStringValueAsReference(
            $inscription,
            $zeroRow->getPhotoFileNames(),
            static function () use ($zeroRow): void {
                $zeroRow->setPhotoFileNames(null);
            },
            static function (Interpretation $reference) use ($zeroRow): void {
                $zeroRow->getPhotoFileNamesReferences()->add($reference);
            }
        );

        $this->tryUseStringValueAsReference(
            $inscription,
            $zeroRow->getSketchFileNames(),
            static function () use ($zeroRow): void {
                $zeroRow->setSketchFileNames(null);
            },
            static function (Interpretation $reference) use ($zeroRow): void {
                $zeroRow->getSketchFileNamesReferences()->add($reference);
            }
        );

        return $zeroRow;
    }

    private function parseAsCollectionOfNamedEntities(
        NamedEntityRepository $repository,
        string $formattedPieces
    ): Collection {
        $splitResult = preg_split('/('.self::MANY_TO_MANY_SEPARATOR.')+(?![^\[]*\])/', $formattedPieces);

        if (false === $splitResult) {
            throw new InvalidArgumentException('Splitting by reference pattern error');
        }

        $formattedPiecesArray = array_filter(
            $splitResult,
            static function (string $formattedPiece): bool {
                return '' !== $formattedPiece;
            }
        );

        return new ArrayCollection(
            array_map(
                [$repository, 'findOneByNameOrCreate'],
                $formattedPiecesArray
            )
        );
    }

    private function createInvalidEntityTypeException(object $entity): InvalidArgumentException
    {
        return new InvalidArgumentException(
            sprintf(
                'Invalid entity type "%s", expected to get "%s"',
                \get_class($entity),
                Inscription::class
            )
        );
    }

    private function createInvalidNewRowKeyException(string $newRowKey): InvalidArgumentException
    {
        return new InvalidArgumentException(
            sprintf(
                'Invalid new row key "%s", expected to get "%s", "%s" or "%s"',
                $newRowKey,
                self::NEW_INSCRIPTION_ROW_KEY,
                self::NEW_ZERO_ROW_KEY,
                self::NEW_INTERPRETATION_ROW_KEY
            )
        );
    }

    private function createUnexpectedEntityTypeException(object $entity, string $methodName): LogicException
    {
        $entityType = \get_class($entity);

        return new LogicException(sprintf('Unexpected entity type "%s" in "%s" method', $entityType, $methodName));
    }

    private function tryUseStringValueAsReference(
        Inscription $inscription,
        ?string $value,
        callable $valueRemover,
        callable $referenceAdder
    ): void {
        if (null !== $value) {
            if (\is_string($value)) {
                $matchResult = preg_match('/^\[\[(.+?)\]\](?:, \[\[(.+?)\]\])*$/', $value, $matches);

                if (false === $matchResult) {
                    throw new InvalidArgumentException('Reference pattern matching error');
                }

                if (1 === $matchResult) {
                    $matchesCount = \count($matches);

                    for ($matchIndex = 1; $matchIndex < $matchesCount; ++$matchIndex) {
                        $referenceSource = $matches[$matchIndex];

                        $reference = $this->interpretationRepository->findOneBySource($inscription, $referenceSource);

                        if (null === $reference) {
                            throw new InvalidArgumentException(sprintf('Reference "%s" not found', $referenceSource));
                        }

                        $valueRemover();
                        $referenceAdder($reference);
                    }
                }
            }
        }
    }

    private function tryUseNamedEntityAsReference(
        Inscription $inscription,
        ?NamedEntityInterface $value,
        callable $valueSetter,
        callable $referencesSetter
    ): void {
        if (null !== $value) {
            $entityName = $value->getName();

            $this->tryUseStringValueAsReference(
                $inscription,
                $entityName,
                $valueSetter,
                $referencesSetter
            );
        }
    }
}
