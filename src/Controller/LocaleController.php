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

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class LocaleController extends AbstractController
{
    /**
     * @Route("/locale/{locale}", name="locale__set", methods={"GET"})
     */
    public function setLocale(Request $request, string $locale): Response
    {
        $availableLocales = $this->getParameter('locales');
        if (!\is_array($availableLocales)) {
            $split = preg_split('/\s*\|\s*/', (string) $availableLocales, -1, PREG_SPLIT_NO_EMPTY);
            $availableLocales = \is_array($split) ? $split : [];
        }

        if (!\in_array($locale, $availableLocales, true)) {
            $locale = (string) $this->getParameter('locale');
        }

        if ($request->hasSession()) {
            $request->getSession()->set('_locale', $locale);
        }
        $request->setLocale($locale);

        $response = $this->redirect($request->headers->get('referer') ?? $this->generateUrl('index'));
        $response->headers->setCookie(
            Cookie::create('_locale')
                ->withValue($locale)
                ->withPath('/')
                ->withExpires(new \DateTime('+1 year'))
        );

        return $response;
    }
}
