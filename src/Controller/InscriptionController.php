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

use App\Persistence\Entity\Epigraphy\Inscription;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Vyfony\Bundle\FilterableTableBundle\Table\TableInterface;

/**
 * @Route("/inscription")
 */
final class InscriptionController extends AbstractController
{
    /**
     * @Route("/list", name="inscription__list", methods={"GET"})
     * @Template("inscription/list.html.twig")
     */
    public function list(TableInterface $filterableTable): array
    {
        return [
            'controller' => 'inscription',
            'method' => 'list',
            'filterForm' => $filterableTable->getFormView(),
            'table' => $filterableTable->getTableMetadata(),
        ];
    }

    /**
     * @Route("/show/{id}", name="inscription__show", methods={"GET"})
     * @Template("inscription/show.html.twig")
     */
    public function show(Inscription $inscription): array
    {
        return [
            'controller' => 'inscription',
            'method' => 'show',
            'inscription' => $inscription,
        ];
    }
}
