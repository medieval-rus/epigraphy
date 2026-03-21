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

namespace App\Services\Epigraphy\ActualValue\Formatter;

use App\Models\StringActualValue;
use App\Services\Epigraphy\OriginalText\Formatter\OriginalTextFormatterInterface;
use App\Services\Epigraphy\OriginalText\Parser\OriginalTextParserInterface;
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
        $description = $actualValue->getDescription();
        $shouldAppendDescription = true;

        switch ($formatType) {
            case self::FORMAT_TYPE_DEFAULT:
                $formattedValue = nl2br(htmlspecialchars($value));

                break;

            case self::FORMAT_TYPE_ORIGINAL_TEXT:
                $formattedValue = $this->originalTextFormatter->format($this->originalTextParser->parse($value));

                break;

            case self::FORMAT_TYPE_ORIGINAL_TEXT_PLAIN:
                $formattedValue = $this->originalTextFormatter->format($this->originalTextParser->parse($value));
                $shouldAppendDescription = false;

                break;
            
            case self::FORMAT_TYPE_TRANSLATION:
                $formattedValue = nl2br($value);
                $formattedValue = $this->appendAiBadgeIfNeeded($actualValue, $formattedValue);
                
                break;

            case self::FORMAT_TYPE_HTML:
                $formattedValue = nl2br($value);
                $formattedValue = $this->appendAiBadgeIfNeeded($actualValue, $formattedValue);

                break;

            default:
                throw new InvalidArgumentException(sprintf('Unknown value format type "%s"', $formatType));
        }

        if (!$shouldAppendDescription) {
            return $formattedValue;
        }

        if (null === $description) {
            return $formattedValue;
        }

        $description = trim($description);
        $description = preg_replace('/^<p>(.*)<\\/p>$/is', '$1', $description);
        $description = preg_replace('/^(?:<br\\s*\\/?>|&nbsp;|\\s)+/i', '', $description);

        return sprintf('%s (%s)', $formattedValue, $description);
    }

    private function appendAiBadgeIfNeeded(StringActualValue $actualValue, string $formattedValue): string
    {
        if (!$actualValue->isAiGenerated()) {
            return $formattedValue;
        }

        $badgeText = $this->translator->trans('translation.aiBadge');
        $safeBadgeText = htmlspecialchars($badgeText, ENT_QUOTES, 'UTF-8');
        $badgeHtml = '<span class="eomr-ai-translation-badge">'.$safeBadgeText.'</span>';

        if (preg_match('/<\/p>\s*$/i', $formattedValue)) {
            return (string) preg_replace('/<\/p>\s*$/i', ' '.$badgeHtml.'</p>', $formattedValue, 1);
        }

        return $formattedValue.' '.$badgeHtml;
    }
}
