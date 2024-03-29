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

namespace App\Services\Epigraphy\OriginalText\Formatter;

use App\Helper\TypeHelper;
use App\Services\Epigraphy\OriginalText\Parser\Models\OriginalText;
use App\Services\Epigraphy\OriginalText\Parser\Models\TextPiece\CommentTextPiece;
use App\Services\Epigraphy\OriginalText\Parser\Models\TextPiece\LigatureTextPiece;
use App\Services\Epigraphy\OriginalText\Parser\Models\TextPiece\OriginalTextPiece;
use App\Services\Epigraphy\OriginalText\Parser\Models\TextPiece\SuperscriptedTextPiece;
use App\Services\Epigraphy\OriginalText\Parser\Models\TextPiece\TextBreakTextPiece;
use App\Services\Epigraphy\OriginalText\Parser\Models\TextPiece\TextPieceInterface;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OriginalTextFormatter implements OriginalTextFormatterInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function format(OriginalText $originalText): string
    {
        return '<div class="eomr-text-wrapper">'.
            implode('', array_map([$this, 'formatTextPiece'], $originalText->getPieces())).
            '</div>';
    }

    private function formatTextPiece(TextPieceInterface $textPiece): string
    {
        switch (true) {
            case $textPiece instanceof LigatureTextPiece:
                return $this->formatLigatureTextPiece($textPiece);
            case $textPiece instanceof CommentTextPiece:
                return $this->formatCommentTextPiece($textPiece);
            case $textPiece instanceof OriginalTextPiece:
                return $this->formatOriginalTextPiece($textPiece);
            case $textPiece instanceof TextBreakTextPiece:
                return $this->formatTextBreakTextPiece($textPiece);
            case $textPiece instanceof SuperscriptedTextPiece:
                return $this->formatSuperscriptedTextPiece($textPiece);
            default:
                throw new InvalidArgumentException(
                    sprintf('Unknown text piece type "%s".', TypeHelper::getTypeName($textPiece))
                );
        }
    }

    private function formatLigatureTextPiece(LigatureTextPiece $textPiece): string
    {
        $text = nl2br($textPiece->getText());

        $hintText = htmlspecialchars(
            $this->translator->trans(
                'originalText.ligature',
                [
                    '%symbols%' => implode('', $textPiece->getLetters()),
                ]
            )
        );

        return '<span aria-label="'.$hintText.'" data-microtip-position="top" role="tooltip" >'.
               '<span class="eomr-text-piece-ligature">'.$text.'</span>'.
               '</span>';
    }

    private function formatCommentTextPiece(CommentTextPiece $textPiece): string
    {
        return '<span class="eomr-text-piece-comment">'.nl2br($textPiece->getText()).'</span>';
    }

    private function formatOriginalTextPiece(OriginalTextPiece $textPiece): string
    {
        return '<span class="eomr-text-piece-original">'.nl2br($textPiece->getText()).'</span>';
    }

    private function formatTextBreakTextPiece(TextPieceInterface $textPiece): string
    {
        return '<span class="eomr-text-piece-text-break font-italic">'.nl2br($textPiece->getText()).'</span>';
    }

    private function formatSuperscriptedTextPiece(SuperscriptedTextPiece $textPiece)
    {
        return '<span class="eomr-text-piece-superscripted">'.nl2br($textPiece->getText()).'</span>';
    }
}
