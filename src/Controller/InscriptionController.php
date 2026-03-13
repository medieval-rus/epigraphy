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

use App\FilterableTable\InscriptionsFilterConfigurator;
use App\Persistence\Entity\Epigraphy\Inscription;
use App\Persistence\Repository\Content\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vyfony\Bundle\FilterableTableBundle\Table\TableInterface;

/**
 * @Route("/inscription")
 */
final class InscriptionController extends AbstractController
{

    /**
     * @Route("/longlist", name="inscription__longlist", methods={"GET"})
     */
    public function longlist(PostRepository $postRepository, TableInterface $filterableTable, InscriptionsFilterConfigurator $filterConfigurator): Response
    {
        return $this->render(
            'site/inscription/list_with_grouped_filters.html.twig', 
            [
                'translationContext' => 'controller.inscription.list',
                'assetsContext' => 'inscription/list',
                'filterForm' => $filterableTable->getFormView(),
                'table' => $filterableTable->getTableMetadata(),
                'filterConfigurator' => $filterConfigurator,
                'post' => $postRepository->findDatabase()
            ]
        );
    }

    /**
     * @Route("/show/{id}", name="inscription__show", methods={"GET"})
     */
    public function show(Inscription $inscription): Response
    {
        if (!$inscription->getIsShownOnSite() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $epidocXml = null;
        $projectDir = (string) $this->getParameter('kernel.project_dir');
        $epidocDir = $projectDir . '/assets/epidoc';
        $candidateNames = [];

        $inscriptionNumber = $inscription->getNumber();
        if ($inscriptionNumber !== null) {
            $inscriptionNumber = trim($inscriptionNumber);
            if ($inscriptionNumber !== '') {
                $candidateNames[] = $inscriptionNumber;
            }
        }

        $inscriptionId = $inscription->getId();
        if ($inscriptionId !== null) {
            $candidateNames[] = (string) $inscriptionId;
        }

        $candidateNames = array_values(array_unique($candidateNames));
        foreach ($candidateNames as $candidateName) {
            if (str_contains($candidateName, '/') || str_contains($candidateName, '\\') || str_contains($candidateName, '..')) {
                continue;
            }

            $epidocPath = $epidocDir . '/' . $candidateName . '.xml';
            if (!is_file($epidocPath) || !is_readable($epidocPath)) {
                continue;
            }

            $fileContent = file_get_contents($epidocPath);
            if ($fileContent !== false) {
                $epidocXml = $fileContent;
                break;
            }
        }

        return $this->render(
            'site/inscription/show.html.twig',
            [
                'translationContext' => 'controller.inscription.show',
                'assetsContext' => 'inscription/show',
                'inscription' => $inscription,
                'epidocXml' => $epidocXml,
            ]
        );
    }
}
