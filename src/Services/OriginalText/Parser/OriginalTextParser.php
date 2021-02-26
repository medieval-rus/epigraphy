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

namespace App\Services\OriginalText\Parser;

use App\Services\OriginalText\Parser\Models\OriginalText;
use App\Services\OriginalText\Parser\Models\TextPiece\OriginalTextPiece;
use App\Services\OriginalText\Parser\Models\TextPiece\TextPieceInterface;
use App\Services\OriginalText\Parser\Models\TextPiece\UnhandledTextAreaInterface;
use App\Services\OriginalText\Parser\RuleParser\RuleParserInterface;
use App\Services\OriginalText\Parser\Rules\RuleDefinitionInterface;

final class OriginalTextParser implements OriginalTextParserInterface
{
    /**
     * @var RuleParserInterface
     */
    private $ruleParser;

    /**
     * @var RuleDefinitionInterface[]
     */
    private $rules;

    /**
     * @param RuleDefinitionInterface[] $rules
     */
    public function __construct(RuleParserInterface $ruleParser, array $rules)
    {
        $this->ruleParser = $ruleParser;
        $this->rules = $rules;
    }

    public function parse(string $originalText): OriginalText
    {
        return new OriginalText($this->applyRuleParsers($originalText));
    }

    /**
     * @return TextPieceInterface[]
     */
    private function applyRuleParsers(string $text, int $initialRuleParserIndex = 0): array
    {
        $rule = $this->getRule($initialRuleParserIndex);

        if (null === $rule) {
            return [new OriginalTextPiece($text)];
        }

        $parseResults = $this->ruleParser->parse($rule, $text);

        $finalResult = [];

        foreach ($parseResults as $parseResult) {
            if ($parseResult instanceof UnhandledTextAreaInterface) {
                foreach ($this->applyRuleParsers($parseResult->getText(), $initialRuleParserIndex + 1) as $textPiece) {
                    $finalResult[] = $textPiece;
                }
            } else {
                $finalResult[] = $parseResult;
            }
        }

        return $finalResult;
    }

    private function getRule(int $index): ?RuleDefinitionInterface
    {
        return $this->rules[$index] ?? null;
    }
}
