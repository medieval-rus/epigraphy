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

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class LocaleSubscriber implements EventSubscriberInterface
{
    private const LOCALE_KEY = '_locale';

    /**
     * @var string[]
     */
    private array $availableLocales;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $locales = $parameterBag->get('locales');
        if (\is_string($locales)) {
            $locales = preg_split('/\s*\|\s*/', $locales, -1, PREG_SPLIT_NO_EMPTY);
        }

        $this->availableLocales = \is_array($locales) ? $locales : [];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 20],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $locale = null;
        if ($request->hasSession() && $request->getSession()->has(self::LOCALE_KEY)) {
            $locale = $request->getSession()->get(self::LOCALE_KEY);
        }

        if ($locale === null) {
            $cookieLocale = $request->cookies->get(self::LOCALE_KEY);
            if ($cookieLocale !== null) {
                $locale = $cookieLocale;
                if ($request->hasSession()) {
                    $request->getSession()->set(self::LOCALE_KEY, $locale);
                }
            }
        }

        if (\is_string($locale) && $this->isAllowedLocale($locale)) {
            $request->setLocale($locale);
        }
    }

    private function isAllowedLocale(string $locale): bool
    {
        return \in_array($locale, $this->availableLocales, true);
    }
}
