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
    public function Compare(BibliographicRecord $a, BibliographicRecord $b): int
    {
        $aShortName = $a->getShortName();
        $bShortName = $b->getShortName();

        $pattern = '/^[а-яёА-ЯЁ].*$/u';

        $aIsCyrillic = 1 === preg_match($pattern, $aShortName);
        $bIsCyrillic = 1 === preg_match($pattern, $bShortName);

        if ($aIsCyrillic && !$bIsCyrillic) {
            return -1;
        }

        if (!$aIsCyrillic && $bIsCyrillic) {
            return 1;
        }

        if (!$aIsCyrillic && !$bIsCyrillic) {
            return strnatcmp($aShortName, $bShortName);
        }

        return strnatcmp($this->replaceJo($aShortName), $this->replaceJo($bShortName));
    }

    private function replaceJo(string $input): string
    {
        return str_replace(['ё', 'Ё'], ['ея', 'Ея'], $input);
    }
}
