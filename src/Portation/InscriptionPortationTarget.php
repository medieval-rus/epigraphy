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
use App\Persistence\Entity\Inscription\Inscription;
use App\Persistence\Entity\Inscription\Interpretation;
use App\Persistence\Entity\NamedEntityInterface;
use App\Persistence\Repository\AlphabetRepository;
use App\Persistence\Repository\ContentCategoryRepository;
use App\Persistence\Repository\Inscription\InscriptionRepository;
use App\Persistence\Repository\MaterialRepository;
use App\Persistence\Repository\PreservationStateRepository;
use App\Persistence\Repository\WritingMethodRepository;
use App\Persistence\Repository\WritingTypeRepository;
use App\Portation\Formatter\Carrier\CarrierFormatterInterface;
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

    private const NEW_INTERPRETATION_ROW_KEY = '++';

    private const MATERIAL_SEPARATOR = ', ';

    /**
     * @var BoolFormatterInterface
     */
    private $boolFormatter;

    /**
     * @var CarrierFormatterInterface
     */
    private $carrierFormatter;

    /**
     * @var InscriptionRepository
     */
    private $inscriptionRepository;

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
        BoolFormatterInterface $boolFormatter,
        CarrierFormatterInterface $carrierFormatter,
        InscriptionRepository $inscriptionRepository,
        WritingTypeRepository $writingTypeRepository,
        MaterialRepository $materialRepository,
        WritingMethodRepository $writingMethodRepository,
        PreservationStateRepository $preservationStateRepository,
        AlphabetRepository $alphabetRepository,
        ContentCategoryRepository $contentCategoryRepository
    ) {
        $this->boolFormatter = $boolFormatter;
        $this->carrierFormatter = $carrierFormatter;
        $this->inscriptionRepository = $inscriptionRepository;
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

        switch (true) {
            case $entity instanceof Inscription:
                return [
                    'id' => (string) $entity->getId(),
                    'inscription.carrier' => $this->carrierFormatter->format($entity->getCarrier()),
                    'inscription.isInSitu' => $this->boolFormatter->format($entity->getIsInSitu()),
                    'inscription.placeOnCarrier' => $entity->getPlaceOnCarrier(),
                    'inscription.writingType' => $formatNamedEntity($entity->getWritingType()),
                    'inscription.materials' => implode(
                        self::MATERIAL_SEPARATOR,
                        $entity->getMaterials()->map($formatNamedEntity)->toArray()
                    ),
                    'inscription.writingMethod' => $formatNamedEntity($entity->getWritingMethod()),
                    'inscription.preservationState' => $formatNamedEntity($entity->getPreservationState()),
                    'inscription.alphabet' => $formatNamedEntity($entity->getAlphabet()),
                    'inscription.contentCategory' => $formatNamedEntity($entity->getContentCategory()),
                    'inscription.dateInText' => $entity->getDateInText(),
                ];
            case $entity instanceof Interpretation:
                return [
                    'id' => sprintf(
                        '%d.%d',
                        $entity->getInscription()->getId(),
                        $entity->getId()
                    ),
                    'interpretation.source' => $entity->getSource(),
                    'interpretation.doWeAgree' => $this->boolFormatter->format($entity->getDoWeAgree()),
                    'interpretation.text' => $entity->getText(),
                    'interpretation.textImageFileName' => $entity->getTextImageFileName(),
                    'interpretation.transliteration' => $entity->getTransliteration(),
                    'interpretation.translation' => $entity->getTranslation(),
                    'interpretation.photoFileName' => $entity->getPhotoFileName(),
                    'interpretation.sketchFileName' => $entity->getSketchFileName(),
                    'interpretation.date' => $entity->getDate(),
                    'interpretation.commentFileName' => $entity->getCommentFileName(),
                ];
            default:
                throw $this->createInvalidEntityTypeException($entity);
        }
    }

    /**
     * @return callable[]
     */
    public function getCellValueHandlers(string $newRowKey): array
    {
        switch ($newRowKey) {
            case self::NEW_INSCRIPTION_ROW_KEY:
                return [
                    'inscription.carrier' => function (
                        Inscription $inscription,
                        string $formattedCarrier
                    ): void {
                        $inscription->setCarrier(
                            $this->carrierFormatter->parse($formattedCarrier)
                        );
                    },
                    'inscription.isInSitu' => function (
                        Inscription $inscription,
                        string $formattedIsInSitu
                    ): void {
                        $inscription->setIsInSitu(
                            $this->boolFormatter->parse(StringHelper::nullIfEmpty($formattedIsInSitu))
                        );
                    },
                    'inscription.placeOnCarrier' => function (
                        Inscription $inscription,
                        string $formattedPlaceOnCarrier
                    ): void {
                        $inscription->setPlaceOnCarrier(
                            StringHelper::nullIfEmpty($formattedPlaceOnCarrier)
                        );
                    },
                    'inscription.writingType' => function (
                        Inscription $inscription,
                        string $formattedWritingType
                    ): void {
                        $writingTypeName = StringHelper::nullIfEmpty($formattedWritingType);

                        $inscription->setWritingType(
                            null === $writingTypeName
                                ? null
                                : $this->writingTypeRepository->findOneByNameOrCreate($writingTypeName)
                        );
                    },
                    'inscription.materials' => function (
                        Inscription $inscription,
                        string $formattedMaterials
                    ): void {
                        $formattedMaterialsParts = explode(self::MATERIAL_SEPARATOR, $formattedMaterials);

                        foreach ($formattedMaterialsParts as $formattedMaterial) {
                            $inscription->addMaterial(
                                $this->materialRepository->findOneByNameOrCreate($formattedMaterial)
                            );
                        }
                    },
                    'inscription.writingMethod' => function (
                        Inscription $inscription,
                        string $formattedWritingMethod
                    ): void {
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
                    'inscription.alphabet' => function (
                        Inscription $inscription,
                        string $formattedAlphabet
                    ): void {
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
                    'inscription.dateInText' => function (
                        Inscription $inscription,
                        string $formattedDateInText
                    ): void {
                        $inscription->setDateInText(
                            StringHelper::nullIfEmpty($formattedDateInText)
                        );
                    },
                ];
            case self::NEW_INTERPRETATION_ROW_KEY:
                return [
                    'interpretation.source' => function (
                        Interpretation $interpretation,
                        string $formattedSource
                    ): void {
                        $interpretation->setSource(
                            StringHelper::nullIfEmpty($formattedSource)
                        );
                    },
                    'interpretation.doWeAgree' => function (
                        Interpretation $interpretation,
                        string $formattedDoWeAgree
                    ): void {
                        $interpretation->setDoWeAgree(
                            $this->boolFormatter->parse($formattedDoWeAgree)
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
                    'interpretation.textImageFileName' => function (
                        Interpretation $interpretation,
                        string $formattedTextImageFileName
                    ): void {
                        $interpretation->setTextImageFileName(
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
                    'interpretation.photoFileName' => function (
                        Interpretation $interpretation,
                        string $formattedPhotoFileName
                    ): void {
                        $interpretation->setPhotoFileName(
                            StringHelper::nullIfEmpty($formattedPhotoFileName)
                        );
                    },
                    'interpretation.sketchFileName' => function (
                        Interpretation $interpretation,
                        string $formattedSketchFileName
                    ): void {
                        $interpretation->setSketchFileName(
                            StringHelper::nullIfEmpty($formattedSketchFileName)
                        );
                    },
                    'interpretation.date' => function (
                        Interpretation $interpretation,
                        string $formattedDate
                    ): void {
                        $interpretation->setDate(
                            StringHelper::nullIfEmpty($formattedDate)
                        );
                    },
                    'interpretation.commentFileName' => function (
                        Interpretation $interpretation,
                        string $formattedCommentFileName
                    ): void {
                        $interpretation->setCommentFileName(
                            StringHelper::nullIfEmpty($formattedCommentFileName)
                        );
                    },
                ];
            default:
                throw $this->createInvalidNewRowKeyException($newRowKey);
        }
    }

    public function createEntity(string $newRowKey): object
    {
        switch ($newRowKey) {
            case self::NEW_INSCRIPTION_ROW_KEY:
                return new Inscription();
            case self::NEW_INTERPRETATION_ROW_KEY:
                return new Interpretation();
            default:
                throw $this->createInvalidNewRowKeyException($newRowKey);
        }
    }

    public function setNestedEntity(string $entityRowKey, object $entity, object $nestedEntity): void
    {
        $entityRowKeyIsForInscription = self::NEW_INSCRIPTION_ROW_KEY === $entityRowKey;

        $entityIsInscription = $entity instanceof Inscription;

        $nestedEntityIsInterpretation = $nestedEntity instanceof Interpretation;

        switch (true) {
            case $entityRowKeyIsForInscription && $entityIsInscription && $nestedEntityIsInterpretation:
                $entity->addInterpretation($nestedEntity);

                break;
            case self::NEW_INTERPRETATION_ROW_KEY:
                throw $this->createUnexpectedRowKeyException($entityRowKey, __METHOD__);
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
    public function getNestedEntities(object $entity): array
    {
        switch (true) {
            case $entity instanceof Inscription:
                return $entity->getInterpretations()->toArray();
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
            new EntityRow(
                self::NEW_INTERPRETATION_ROW_KEY,
                null
            )
        );
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
                'Invalid new row key "%s", expected to get "%s" or "%s"',
                $newRowKey,
                self::NEW_INSCRIPTION_ROW_KEY,
                self::NEW_INTERPRETATION_ROW_KEY
            )
        );
    }

    private function createUnexpectedRowKeyException(string $rowKey, string $methodName): LogicException
    {
        return new LogicException(sprintf('Unexpected row key "%s" in "%s" method', $rowKey, $methodName));
    }

    private function createUnexpectedEntityTypeException(object $entity, string $methodName): LogicException
    {
        $entityType = \get_class($entity);

        return new LogicException(sprintf('Unexpected entity type "%s" in "%s" method', $entityType, $methodName));
    }
}
