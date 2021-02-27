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

namespace App\Helper;

abstract class StringHelper
{
    public static function emptyIfNull(?string $nullableString): string
    {
        if (null === $nullableString) {
            return '';
        }

        return $nullableString;
    }

    public static function nullIfEmpty(string $string): ?string
    {
        if ('' === $string) {
            return null;
        }

        return $string;
    }

    public static function removeFromStart(string $string, string $search): string
    {
        if (self::startsWith($string, $search)) {
            return substr($string, \strlen($search));
        }

        return $string;
    }

    public static function removeFromEnd(string $string, string $search): string
    {
        if (self::endsWith($string, $search)) {
            return substr($string, 0, \strlen($string) - \strlen($search));
        }

        return $string;
    }

    public static function replaceStart(string $string, string $search, string $replace): string
    {
        if (self::startsWith($string, $search)) {
            return substr_replace($string, $replace, 0, \strlen($search));
        }

        return $string;
    }

    public static function replaceEnd(string $string, string $search, string $replace): string
    {
        if (self::endsWith($string, $search)) {
            return substr_replace($string, $replace, -\strlen($search));
        }

        return $string;
    }

    public static function startsWith(string $string, string $search): bool
    {
        return 0 === strpos($string, $search);
    }

    public static function endsWith(string $string, string $search): bool
    {
        return strpos($string, $search) === \strlen($string) - \strlen($search);
    }

    public static function isLowercased(string $string): bool
    {
        return mb_strtolower($string) === $string;
    }
}
