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

namespace App\Services\Epigraphy\ActualValue\Extractor;

use App\Models\FilesActualValue;
use App\Models\InscriptionActualFile;
use App\Models\StringActualValue;
use App\Persistence\Entity\Epigraphy\Inscription;
use App\Persistence\Entity\Epigraphy\Interpretation;
use App\Persistence\Entity\Epigraphy\NamedEntityInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ActualValueExtractor implements ActualValueExtractorInterface
{
    private PropertyAccessorInterface $propertyAccessor;
    private TranslatorInterface $translator;

    public function __construct(PropertyAccessorInterface $propertyAccessor, TranslatorInterface $translator)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->translator = $translator;
    }

    /**
     * @return StringActualValue[]
     */
    public function extractFromZeroRowAsStrings(Inscription $inscription, string $propertyName): array
    {
        $referenceValueFormatter = function (
            Interpretation $interpretation
        ) use ($propertyName): ?StringActualValue {
            $value = $this->getStringValue($this->propertyAccessor->getValue($interpretation, $propertyName));

            if (null === $value) {
                return null;
            }

            return new StringActualValue(
                $value,
                $interpretation->getSource()->getShortName()
            );
        };

        $zeroRow = $inscription->getZeroRow();

        $references = $this->propertyAccessor->getValue($zeroRow, $propertyName.'References')->toArray();

        usort($references, fn(Interpretation $a, Interpretation $b) =>  $b->getSource()->getYear() - $a->getSource()->getYear());

        $referenceValues = array_map($referenceValueFormatter, $references);

        $zeroRowValue = $this->getStringValue($this->propertyAccessor->getValue($zeroRow, $propertyName));

        if (null !== $zeroRowValue) {
            $allValues = [
                new StringActualValue($zeroRowValue, null),
                ...$referenceValues,
            ];
        } else {
            $allValues = $referenceValues;
        }

        return array_filter($allValues, [$this, 'isNotNull']);
    }

    /**
     * @return FilesActualValue[]
     */
    public function extractFromZeroRowAsFiles(Inscription $inscription, string $propertyName): array
    {
        $referenceValueFormatter = function (Interpretation $interpretation) use ($propertyName): ?FilesActualValue {
            $files = $this->propertyAccessor->getValue($interpretation, $propertyName);

            if ($files instanceof Collection) {
                return new FilesActualValue($files->toArray(), $interpretation);
            }

            return null;
        };

        $zeroRow = $inscription->getZeroRow();

        $references = $this->propertyAccessor->getValue($zeroRow, $propertyName.'References')->toArray();

        $referenceValues = array_map($referenceValueFormatter, $references);

        $zeroRowValue = $this->propertyAccessor->getValue($zeroRow, $propertyName);

        if ($zeroRowValue instanceof Collection) {
            $allValues = [
                new FilesActualValue($zeroRowValue->toArray(), null),
                ...$referenceValues,
            ];
        } else {
            $allValues = $referenceValues;
        }

        return array_filter($allValues, [$this, 'isNotNull']);
    }

    /**
     * @return InscriptionActualFile[]
     */
    public function extractActualFiles(Inscription $inscription, array $properties): array
    {
        $actualValues = array_merge(
            ...array_map(
                fn (string $propertyName): array => $this->extractFromZeroRowAsFiles($inscription, $propertyName),
                $properties
            )
        );

        $result = [];
        foreach ($actualValues as $actualValue) {
            $files = $actualValue->getValue();

            if (0 === \count($files)) {
                continue;
            }

            $formattedInterpretation = null;
            if (null !== $actualValue->getInterpretation()) {
                $formattedInterpretation = $this->translator->trans(
                    'image.source',
                    [
                        '%source%' => $actualValue->getInterpretation()->getSource()->getShortName(),
                    ]
                );
            }

            foreach ($files as $file) {
                $descriptionParts = [];
                if (null !== $formattedInterpretation) {
                    $descriptionParts[] = $formattedInterpretation;
                }

                if (null !== $file->getDescription()) {
                    $descriptionParts[] = $file->getDescription();
                }

                $description = implode('; ', $descriptionParts);

                $result[] = new InscriptionActualFile($file, $description);
            }
        }

        return $result;
    }

    private function getStringValue($value): ?string
    {
        if (\is_string($value) || null === $value) {
            return $value;
        }

        if ($value instanceof NamedEntityInterface) {
            return $value->getName();
        }

        if ($value instanceof Collection) {
            if (0 === $value->count()) {
                return null;
            }

            return implode(
                ', ',
                array_map(
                    static function ($value): string {
                        return (string) $value;
                    },
                    array_filter(
                        array_map([$this, 'getStringValue'], $value->toArray()),
                        [$this, 'isNotNull']
                    )
                )
            );
        }

        return (string) $value;
    }

    private function isNotNull($formattedValue): bool
    {
        return null !== $formattedValue;
    }
}
