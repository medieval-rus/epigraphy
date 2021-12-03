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

namespace App\Services\Sorting\Bibliography;

use App\Persistence\Entity\Bibliography\BibliographicRecord;

final class BibliographicRecordComparer implements BibliographicRecordComparerInterface
{
    private const CyrillicNamePatter = '/^[а-яёА-ЯЁ].*$/u';

    public function compareByName(BibliographicRecord $a, BibliographicRecord $b): int
    {
        $result = $this->compareByNameOnly($a, $b);

        if (0 === $result) {
            return $this->compareByYearOnly($a, $b);
        }

        return $result;
    }

    public function compareByYear(BibliographicRecord $a, BibliographicRecord $b): int
    {
        $result = $this->compareByYearOnly($a, $b);

        if (0 === $result) {
            return $this->compareByNameOnly($a, $b);
        }

        return $result;
    }

    private function compareByYearOnly(BibliographicRecord $a, BibliographicRecord $b): int
    {
        $aYear = $a->getYear();
        $bYear = $b->getYear();

        if ($aYear === $bYear) {
            return 0;
        }

        if (null === $aYear) {
            return 1;
        }

        if (null === $bYear) {
            return -1;
        }

        return $aYear > $bYear ? 1 : -1;
    }

    private function compareByNameOnly(BibliographicRecord $a, BibliographicRecord $b): int
    {
        $aShortName = $a->getShortName();
        $bShortName = $b->getShortName();

        $aIsCyrillic = 1 === preg_match(self::CyrillicNamePatter, $aShortName);
        $bIsCyrillic = 1 === preg_match(self::CyrillicNamePatter, $bShortName);

        if ($aIsCyrillic && !$bIsCyrillic) {
            return -1;
        }

        if (!$aIsCyrillic && $bIsCyrillic) {
            return 1;
        }

        return !$aIsCyrillic && !$bIsCyrillic
            ? strnatcmp($aShortName, $bShortName)
            : strnatcmp($this->replaceJo($aShortName), $this->replaceJo($bShortName));
    }

    private function replaceJo(string $input): string
    {
        return str_replace(['ё', 'Ё'], ['ея', 'Ея'], $input);
    }
}
