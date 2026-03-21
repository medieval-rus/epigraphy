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

namespace App\Twig;

use App\Services\Epigraphy\Localization\LocalizedTextService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class AppTwigExtension extends AbstractExtension
{
    private TranslatorInterface $translator;
    private LocalizedTextService $localizedTextService;

    public function __construct(TranslatorInterface $translator, LocalizedTextService $localizedTextService)
    {
        $this->translator = $translator;
        $this->localizedTextService = $localizedTextService;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('transWithContext', [$this, 'translateWithContext']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('localizedText', [$this, 'localizedText']),
            new TwigFunction('localizedName', [$this, 'localizedName']),
        ];
    }

    public function translateWithContext(
        string $message,
        string $context,
        ?array $parameters = [],
        ?string $domain = null,
        ?string $locale = null
    ): string {
        return $this->translator->trans($context.'.'.$message, $parameters, $domain, $locale);
    }

    public function localizedText($entity, string $field, ?string $fallbackValue = null, ?string $locale = null): ?string
    {
        $resolvedValue = $this->localizedTextService->resolveForEntity($entity, $field, $fallbackValue, $locale);
        if (null === $resolvedValue) {
            return null;
        }

        if (!$this->localizedTextService->isAiGeneratedForEntity($entity, $field, $locale)) {
            return $resolvedValue;
        }

        $badgeText = htmlspecialchars($this->translator->trans('translation.aiBadge'), ENT_QUOTES, 'UTF-8');
        $badgeHtml = '<span class="eomr-ai-translation-badge">'.$badgeText.'</span>';

        if (preg_match('/<\/p>\s*$/i', $resolvedValue)) {
            return (string) preg_replace('/<\/p>\s*$/i', ' '.$badgeHtml.'</p>', $resolvedValue, 1);
        }

        return $resolvedValue.' '.$badgeHtml;
    }

    public function localizedName($entity, ?string $locale = null): ?string
    {
        if (null === $entity || !method_exists($entity, 'getName')) {
            return null;
        }

        return $this->localizedTextService->resolveForEntity($entity, 'name', $entity->getName(), $locale);
    }
}
