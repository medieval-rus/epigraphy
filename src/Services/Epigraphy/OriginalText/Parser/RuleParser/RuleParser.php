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

namespace App\Services\Epigraphy\OriginalText\Parser\RuleParser;

use App\Services\Epigraphy\OriginalText\Parser\Models\TextPiece\TextPieceInterface;
use App\Services\Epigraphy\OriginalText\Parser\Models\TextPiece\UnhandledTextArea;
use App\Services\Epigraphy\OriginalText\Parser\Rules\RuleDefinitionInterface;

final class RuleParser implements RuleParserInterface
{
    /**
     * @return TextPieceInterface[]
     */
    public function parse(RuleDefinitionInterface $rule, string $text): array
    {
        $pieces = [];

        preg_match_all($rule->getRegex(), $text, $regexMatches);

        $fullMatches = $regexMatches[0];
        $exactMatches = $regexMatches[1];

        foreach ($fullMatches as $index => $match) {
            $explodedText = explode($match, $text, 2);

            if ('' !== $explodedText[0]) {
                $pieces[] = new UnhandledTextArea($explodedText[0]);
            }

            $pieces[] = $rule->createTextPiece($exactMatches[$index]);
            $text = $explodedText[1];
        }

        if ('' !== $text || 0 === \count($pieces)) {
            $pieces[] = new UnhandledTextArea($text);
        }

        return $pieces;
    }
}
