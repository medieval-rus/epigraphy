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

use App\Persistence\Repository\Content\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class InformationController extends AbstractController
{
    /**
     * @Route("/about-site", name="information__about_site")
     */
    public function aboutSite(PostRepository $postRepository): Response
    {
        return $this->render(
            'site/content/post.html.twig',
            [
                'translationContext' => 'controller.information.aboutSite',
                'assetsContext' => 'content/post',
                'post' => $postRepository->findAboutSite(),
            ]
        );
    }

    /**
     * @Route("/news", name="information__news")
     */
    public function news(PostRepository $postRepository): Response
    {
        return $this->render(
            'site/content/post.html.twig',
            [
                'translationContext' => 'controller.information.news',
                'assetsContext' => 'content/post',
                'post' => $postRepository->findNews(),
            ]
        );
    }

    /**
     * @Route("/legend", name="information__legend")
     */
    public function legend(PostRepository $postRepository): Response
    {
        return $this->render(
            'site/content/post.html.twig',
            [
                'translationContext' => 'controller.information.legend',
                'assetsContext' => 'content/post',
                'post' => $postRepository->findLegend(),
            ]
        );
    }
}
