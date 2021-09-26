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

namespace App\Admin\Epigraphy\Models;

use App\Persistence\Entity\Epigraphy\Interpretation;
use App\Persistence\Entity\Epigraphy\ZeroRow;
use Doctrine\Common\Collections\Collection;
use ReflectionObject;
use Symfony\Component\PropertyAccess\PropertyAccessor;

final class AdminInterpretationWrapper extends Interpretation
{
    /**
     * @var bool
     */
    private $isOriginPartOfZeroRow;

    /**
     * @var bool
     */
    private $isPlaceOnCarrierPartOfZeroRow;

    /**
     * @var bool
     */
    private $isWritingTypesPartOfZeroRow;

    /**
     * @var bool
     */
    private $isWritingMethodsPartOfZeroRow;

    /**
     * @var bool
     */
    private $isPreservationStatesPartOfZeroRow;

    /**
     * @var bool
     */
    private $isMaterialsPartOfZeroRow;

    /**
     * @var bool
     */
    private $isAlphabetsPartOfZeroRow;

    /**
     * @var bool
     */
    private $isTextPartOfZeroRow;

    /**
     * @var bool
     */
    private $isTextImagesPartOfZeroRow;

    /**
     * @var bool
     */
    private $isTransliterationPartOfZeroRow;

    /**
     * @var bool
     */
    private $isTranslationPartOfZeroRow;

    /**
     * @var bool
     */
    private $isContentCategoriesPartOfZeroRow;

    /**
     * @var bool
     */
    private $isContentPartOfZeroRow;

    /**
     * @var bool
     */
    private $isDateInTextPartOfZeroRow;

    /**
     * @var bool
     */
    private $isStratigraphicalDatePartOfZeroRow;

    /**
     * @var bool
     */
    private $isNonStratigraphicalDatePartOfZeroRow;

    /**
     * @var bool
     */
    private $isHistoricalDatePartOfZeroRow;

    /**
     * @var Interpretation
     */
    private $source;

    public function __construct(Interpretation $interpretation)
    {
        parent::__construct();

        $this->source = $interpretation;

        self::copyValues($this->source, $this, $this->source);

        $zeroRow = $this->getInscription()->getZeroRow();

        $predicate = function ($key, Interpretation $existingInterpretation): bool {
            return $existingInterpretation->getId() === $this->source->getId();
        };

        $this->isOriginPartOfZeroRow = $zeroRow->getOriginReferences()->exists($predicate);
        $this->isPlaceOnCarrierPartOfZeroRow = $zeroRow->getPlaceOnCarrierReferences()->exists($predicate);
        $this->isWritingTypesPartOfZeroRow = $zeroRow->getWritingTypesReferences()->exists($predicate);
        $this->isWritingMethodsPartOfZeroRow = $zeroRow->getWritingMethodsReferences()->exists($predicate);
        $this->isPreservationStatesPartOfZeroRow = $zeroRow->getPreservationStatesReferences()->exists($predicate);
        $this->isMaterialsPartOfZeroRow = $zeroRow->getMaterialsReferences()->exists($predicate);
        $this->isAlphabetsPartOfZeroRow = $zeroRow->getAlphabetsReferences()->exists($predicate);
        $this->isTextPartOfZeroRow = $zeroRow->getTextReferences()->exists($predicate);
        $this->isTextImagesPartOfZeroRow = $zeroRow->getTextImagesReferences()->exists($predicate);
        $this->isTransliterationPartOfZeroRow = $zeroRow->getTransliterationReferences()->exists($predicate);
        $this->isTranslationPartOfZeroRow = $zeroRow->getTranslationReferences()->exists($predicate);
        $this->isContentCategoriesPartOfZeroRow = $zeroRow->getContentCategoriesReferences()->exists($predicate);
        $this->isContentPartOfZeroRow = $zeroRow->getContentReferences()->exists($predicate);
        $this->isDateInTextPartOfZeroRow = $zeroRow->getDateInTextReferences()->exists($predicate);
        $this->isStratigraphicalDatePartOfZeroRow = $zeroRow->getStratigraphicalDateReferences()->exists($predicate);
        $this->isNonStratigraphicalDatePartOfZeroRow = $zeroRow
            ->getNonStratigraphicalDateReferences()
            ->exists($predicate);
        $this->isHistoricalDatePartOfZeroRow = $zeroRow->getHistoricalDateReferences()->exists($predicate);
    }

    public function toInterpretation(): Interpretation
    {
        $interpretation = $this->source;

        self::copyValues($this, $interpretation, $interpretation);

        return $interpretation;
    }

    public function updateZeroRow(ZeroRow $zeroRow): void
    {
        $this->updateReferences($zeroRow->getOriginReferences(), $this->isOriginPartOfZeroRow);
        $this->updateReferences($zeroRow->getPlaceOnCarrierReferences(), $this->isPlaceOnCarrierPartOfZeroRow);
        $this->updateReferences($zeroRow->getWritingTypesReferences(), $this->isWritingTypesPartOfZeroRow);
        $this->updateReferences($zeroRow->getWritingMethodsReferences(), $this->isWritingMethodsPartOfZeroRow);
        $this->updateReferences($zeroRow->getPreservationStatesReferences(), $this->isPreservationStatesPartOfZeroRow);
        $this->updateReferences($zeroRow->getMaterialsReferences(), $this->isMaterialsPartOfZeroRow);
        $this->updateReferences($zeroRow->getAlphabetsReferences(), $this->isAlphabetsPartOfZeroRow);
        $this->updateReferences($zeroRow->getTextReferences(), $this->isTextPartOfZeroRow);
        $this->updateReferences($zeroRow->getTextImagesReferences(), $this->isTextImagesPartOfZeroRow);
        $this->updateReferences($zeroRow->getTransliterationReferences(), $this->isTransliterationPartOfZeroRow);
        $this->updateReferences($zeroRow->getTranslationReferences(), $this->isTranslationPartOfZeroRow);
        $this->updateReferences($zeroRow->getContentCategoriesReferences(), $this->isContentCategoriesPartOfZeroRow);
        $this->updateReferences($zeroRow->getContentReferences(), $this->isContentPartOfZeroRow);
        $this->updateReferences($zeroRow->getDateInTextReferences(), $this->isDateInTextPartOfZeroRow);
        $this->updateReferences(
            $zeroRow->getStratigraphicalDateReferences(),
            $this->isStratigraphicalDatePartOfZeroRow
        );
        $this->updateReferences(
            $zeroRow->getNonStratigraphicalDateReferences(),
            $this->isNonStratigraphicalDatePartOfZeroRow
        );
        $this->updateReferences($zeroRow->getHistoricalDateReferences(), $this->isHistoricalDatePartOfZeroRow);
    }

    public function getIsOriginPartOfZeroRow(): ?bool
    {
        return $this->isOriginPartOfZeroRow;
    }

    public function setIsOriginPartOfZeroRow(?bool $isOriginPartOfZeroRow): void
    {
        $this->isOriginPartOfZeroRow = $isOriginPartOfZeroRow;
    }

    public function getIsPlaceOnCarrierPartOfZeroRow(): ?bool
    {
        return $this->isPlaceOnCarrierPartOfZeroRow;
    }

    public function setIsPlaceOnCarrierPartOfZeroRow(?bool $isPlaceOnCarrierPartOfZeroRow): void
    {
        $this->isPlaceOnCarrierPartOfZeroRow = $isPlaceOnCarrierPartOfZeroRow;
    }

    public function getIsWritingTypesPartOfZeroRow(): ?bool
    {
        return $this->isWritingTypesPartOfZeroRow;
    }

    public function setIsWritingTypesPartOfZeroRow(?bool $isWritingTypesPartOfZeroRow): void
    {
        $this->isWritingTypesPartOfZeroRow = $isWritingTypesPartOfZeroRow;
    }

    public function getIsWritingMethodsPartOfZeroRow(): ?bool
    {
        return $this->isWritingMethodsPartOfZeroRow;
    }

    public function setIsWritingMethodsPartOfZeroRow(?bool $isWritingMethodsPartOfZeroRow): void
    {
        $this->isWritingMethodsPartOfZeroRow = $isWritingMethodsPartOfZeroRow;
    }

    public function getIsPreservationStatesPartOfZeroRow(): ?bool
    {
        return $this->isPreservationStatesPartOfZeroRow;
    }

    public function setIsPreservationStatesPartOfZeroRow(?bool $isPreservationStatesPartOfZeroRow): void
    {
        $this->isPreservationStatesPartOfZeroRow = $isPreservationStatesPartOfZeroRow;
    }

    public function getIsMaterialsPartOfZeroRow(): ?bool
    {
        return $this->isMaterialsPartOfZeroRow;
    }

    public function setIsMaterialsPartOfZeroRow(?bool $isMaterialsPartOfZeroRow): void
    {
        $this->isMaterialsPartOfZeroRow = $isMaterialsPartOfZeroRow;
    }

    public function getIsAlphabetsPartOfZeroRow(): ?bool
    {
        return $this->isAlphabetsPartOfZeroRow;
    }

    public function setIsAlphabetsPartOfZeroRow(?bool $isAlphabetsPartOfZeroRow): void
    {
        $this->isAlphabetsPartOfZeroRow = $isAlphabetsPartOfZeroRow;
    }

    public function getIsTextPartOfZeroRow(): ?bool
    {
        return $this->isTextPartOfZeroRow;
    }

    public function setIsTextPartOfZeroRow(?bool $isTextPartOfZeroRow): void
    {
        $this->isTextPartOfZeroRow = $isTextPartOfZeroRow;
    }

    public function getIsTextImagesPartOfZeroRow(): ?bool
    {
        return $this->isTextImagesPartOfZeroRow;
    }

    public function setIsTextImagesPartOfZeroRow(?bool $isTextImagesPartOfZeroRow): void
    {
        $this->isTextImagesPartOfZeroRow = $isTextImagesPartOfZeroRow;
    }

    public function getIsTransliterationPartOfZeroRow(): ?bool
    {
        return $this->isTransliterationPartOfZeroRow;
    }

    public function setIsTransliterationPartOfZeroRow(?bool $isTransliterationPartOfZeroRow): void
    {
        $this->isTransliterationPartOfZeroRow = $isTransliterationPartOfZeroRow;
    }

    public function getIsTranslationPartOfZeroRow(): ?bool
    {
        return $this->isTranslationPartOfZeroRow;
    }

    public function setIsTranslationPartOfZeroRow(?bool $isTranslationPartOfZeroRow): void
    {
        $this->isTranslationPartOfZeroRow = $isTranslationPartOfZeroRow;
    }

    public function getIsContentCategoriesPartOfZeroRow(): ?bool
    {
        return $this->isContentCategoriesPartOfZeroRow;
    }

    public function setIsContentCategoriesPartOfZeroRow(?bool $isContentCategoriesPartOfZeroRow): void
    {
        $this->isContentCategoriesPartOfZeroRow = $isContentCategoriesPartOfZeroRow;
    }

    public function getIsContentPartOfZeroRow(): ?bool
    {
        return $this->isContentPartOfZeroRow;
    }

    public function setIsContentPartOfZeroRow(?bool $isContentPartOfZeroRow): void
    {
        $this->isContentPartOfZeroRow = $isContentPartOfZeroRow;
    }

    public function getIsDateInTextPartOfZeroRow(): ?bool
    {
        return $this->isDateInTextPartOfZeroRow;
    }

    public function setIsDateInTextPartOfZeroRow(?bool $isDateInTextPartOfZeroRow): void
    {
        $this->isDateInTextPartOfZeroRow = $isDateInTextPartOfZeroRow;
    }

    public function getIsStratigraphicalDatePartOfZeroRow(): ?bool
    {
        return $this->isStratigraphicalDatePartOfZeroRow;
    }

    public function setIsStratigraphicalDatePartOfZeroRow(?bool $isStratigraphicalDatePartOfZeroRow): void
    {
        $this->isStratigraphicalDatePartOfZeroRow = $isStratigraphicalDatePartOfZeroRow;
    }

    public function getIsNonStratigraphicalDatePartOfZeroRow(): ?bool
    {
        return $this->isNonStratigraphicalDatePartOfZeroRow;
    }

    public function setIsNonStratigraphicalDatePartOfZeroRow(?bool $isNonStratigraphicalDatePartOfZeroRow): void
    {
        $this->isNonStratigraphicalDatePartOfZeroRow = $isNonStratigraphicalDatePartOfZeroRow;
    }

    public function getIsHistoricalDatePartOfZeroRow(): ?bool
    {
        return $this->isHistoricalDatePartOfZeroRow;
    }

    public function setIsHistoricalDatePartOfZeroRow(?bool $isHistoricalDatePartOfZeroRow): void
    {
        $this->isHistoricalDatePartOfZeroRow = $isHistoricalDatePartOfZeroRow;
    }

    private function updateReferences(
        Collection $currentReferences,
        bool $isCurrentInterpretationPartOfReferences
    ): void {
        if ($isCurrentInterpretationPartOfReferences) {
            $currentReferences->add($this->toInterpretation());
        }
    }

    private static function copyValues(
        Interpretation $from,
        Interpretation $to,
        Interpretation $propertiesProvider
    ): void {
        $propertyAccessor = new PropertyAccessor();

        foreach ((new ReflectionObject($propertiesProvider))->getProperties() as $property) {
            $propertyAccessor->setValue(
                $to,
                $property->getName(),
                $propertyAccessor->getValue($from, $property->getName())
            );
        }
    }
}
