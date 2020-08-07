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

namespace App\Formatter\ZeroRow;

use App\Persistence\Entity\Epigraphy\Inscription\Inscription;
use App\Persistence\Entity\Epigraphy\Inscription\Interpretation;
use App\Persistence\Entity\Epigraphy\NamedEntityInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ZeroRowFormatter implements ZeroRowFormatterInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function format(Inscription $inscription, string $propertyName): string
    {
        $referenceValueFormatter = function (Interpretation $interpretation) use ($propertyName) {
            return sprintf(
                '%s (%s)',
                $this->formatValue($this->propertyAccessor->getValue($interpretation, $propertyName)),
                $interpretation->getSource()
            );
        };

        $nonEmptyValueFilter = function (?string $formattedValue): bool {
            return null !== $formattedValue;
        };

        $zeroRow = $inscription->getZeroRow();

        $value = $this->propertyAccessor->getValue($zeroRow, $propertyName);

        $references = $this->propertyAccessor->getValue($zeroRow, $propertyName.'References')->toArray();

        $allValues = array_map($referenceValueFormatter, $references);

        array_unshift($allValues, $value);

        $formattedValues = array_map([$this, 'formatValue'], $allValues);

        return implode(PHP_EOL, array_filter($formattedValues, $nonEmptyValueFilter));
    }

    private function formatValue($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (\is_string($value)) {
            return $value;
        }

        if ($value instanceof NamedEntityInterface) {
            return $value->getName();
        }

        if ($value instanceof Collection) {
            if (0 === $value->count()) {
                return null;
            }

            return implode(', ', array_map([$this, 'formatValue'], $value->toArray()));
        }

        return (string) $value;
    }
}
