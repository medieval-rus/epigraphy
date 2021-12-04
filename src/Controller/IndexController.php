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

use App\Persistence\Repository\Content\InscriptionListRepository;
use App\Persistence\Repository\Content\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(PostRepository $postRepository, InscriptionListRepository $inscriptionListRepository): Response
    {
        $favoriteInscriptions = $inscriptionListRepository->findFavoriteInscriptions()->toArray();

        shuffle($favoriteInscriptions);

        $favoriteInscriptions = \array_slice(
            $favoriteInscriptions,
            0,
            $this->getParameter('favorite_inscriptions_count')
        );

        return $this->render(
            'site/index/index.html.twig',
            [
                'translationContext' => 'controller.index.index',
                'assetsContext' => 'index/index',
                'inscriptions' => $favoriteInscriptions,
                'post' => $postRepository->findIndex(),
            ]
        );
    }
}
