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

namespace App\Portation\Formatter\Bool;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class BoolFormatter implements BoolFormatterInterface
{
    public const BOOL_TRUE = 'да';

    public const BOOL_FALSE = 'нет';

    public function format(?bool $bool): ?string
    {
        if (null === $bool) {
            return null;
        }

        return $bool ? self::BOOL_TRUE : self::BOOL_FALSE;
    }

    public function parse(?string $formattedBool): ?bool
    {
        if (null === $formattedBool) {
            return null;
        }

        return self::BOOL_TRUE === $formattedBool;
    }
}
