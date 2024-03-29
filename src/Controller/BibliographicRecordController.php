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

use App\Persistence\Entity\Bibliography\BibliographicRecord;
use App\Persistence\Repository\Content\PostRepository;
use App\Services\Bibliography\Sorting\BibliographicRecordComparerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/bibliography/record")
 */
final class BibliographicRecordController extends AbstractController
{
    /**
     * @Route("/list", name="bibliographic_record__list", methods={"GET"})
     */
    public function list(
        EntityManagerInterface $entityManager,
        PostRepository $postRepository,
        BibliographicRecordComparerInterface $comparer
    ): Response {
        $records = $entityManager
            ->getRepository(BibliographicRecord::class)
            ->findBy([], ['shortName' => 'ASC']);

        usort($records, [$comparer, 'compareByName']);

        return $this->render(
            'site/bibliography/list.html.twig',
            [
                'translationContext' => 'controller.bibliographic-record.list',
                'assetsContext' => 'bibliographic-record/list',
                'records' => $records,
                'post' => $postRepository->findBibliography(),
            ]
        );
    }
}
