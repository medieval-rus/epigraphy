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

namespace App\Services\ActualValue\Formatter;

use App\Models\StringActualValue;
use App\Services\OriginalText\Formatter\OriginalTextFormatterInterface;
use App\Services\OriginalText\Parser\OriginalTextParserInterface;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ActualValueFormatter implements ActualValueFormatterInterface
{
    private OriginalTextParserInterface $originalTextParser;

    private OriginalTextFormatterInterface $originalTextFormatter;

    private TranslatorInterface $translator;

    public function __construct(
        OriginalTextParserInterface $originalTextParser,
        OriginalTextFormatterInterface $originalTextFormatter,
        TranslatorInterface $translator
    ) {
        $this->originalTextParser = $originalTextParser;
        $this->originalTextFormatter = $originalTextFormatter;
        $this->translator = $translator;
    }

    public function format(StringActualValue $actualValue, string $formatType): string
    {
        $value = $actualValue->getValue();
        $description = $actualValue->getDescription() ?? $this->translator->trans('actualValue.original');

        switch ($formatType) {
            case self::FORMAT_TYPE_DEFAULT:
                $formattedValue = nl2br(htmlspecialchars($value));
                break;

            case self::FORMAT_TYPE_ORIGINAL_TEXT:
                $formattedValue = $this->originalTextFormatter->format($this->originalTextParser->parse($value));
                break;

            default:
                throw new InvalidArgumentException(sprintf('Unknown value format type "%s"', $formatType));
        }

        return sprintf('%s (%s)', $formattedValue, $description);
    }
}
