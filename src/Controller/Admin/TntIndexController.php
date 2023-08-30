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

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sonata\AdminBundle\Controller\CRUDController;

use TeamTNT\TNTSearch\TNTSearch;


final class TntIndexController extends CRUDController
{
    /**
     * Public function for building a TNTSearch index.
     */
    public function indexAction(): Response
    {
        $search = new TNTSearch;
        $config = $this->loadTNTConfig();
        $search->loadConfig($config);
        $search->fuzziness = true;

        $indexer = $search->createIndex('/thumbs/fulltext.sql');
        $indexer->query('SELECT inscription.id, inscription.comment, inscription.date_explanation, 
        zero_row.place_on_carrier AS zr_poc, 
        zero_row.text AS zr_text, zero_row.translation AS zr_translation,
        zero_row.transliteration AS zr_trans, zero_row.description AS zr_desc, 
        zero_row.date_in_text AS zr_dit, zero_row.non_stratigraphical_date AS zr_nsd, 
        zero_row.reconstruction AS zr_rec, zero_row.normalization as zr_norm, 
        zero_row.interpretation_comment as zr_ic, zero_row.origin AS zr_origin,
        interpretation.comment AS int_comment, 
        interpretation.place_on_carrier AS int_poc, 
        interpretation.text AS int_text, interpretation.translation AS int_translation,
        interpretation.transliteration AS int_trans, interpretation.description AS int_desc,
        interpretation.date_in_text AS int_dit, interpretation.non_stratigraphical_date AS int_nsd, 
        interpretation.reconstruction AS int_rec, interpretation.normalization as int_norm, 
        interpretation.interpretation_comment as rec_ic, interpretation.origin AS rec_origin, 
        carrier.find_circumstances, carrier.characteristics, carrier.individual_name,
        carrier.stratigraphical_date, carrier.material_description,
        carrier.carrier_history, carrier.storage_localization 
        FROM inscription 
        INNER JOIN carrier ON inscription.carrier_id = carrier.id 
        INNER JOIN zero_row ON inscription.zero_row_id = zero_row.id 
        INNER JOIN interpretation ON inscription.id = interpretation.inscription_id;');
        $indexer->run();

        // $this->addFlash('sonata_flash_success', $this->trans('action.index.flash'));
        $this->addFlash('sonata_flash_success', $this->trans('Индекс перестроен'));
        return $this->redirectToRoute('sonata_admin_dashboard');
    }

    private function loadTNTConfig(): array {
        $db_url = $_ENV['DATABASE_URL'];
        $db_parameters = parse_url($db_url);

        $config = [
            'driver' => $db_parameters["scheme"],
            'host' => $db_parameters["host"],
            'database' => str_replace('/', '', $db_parameters["path"]),
            'username' => $db_parameters["user"],
            'password' => $db_parameters["pass"],
            'storage' => './',
            'stemmer' => \TeamTNT\TNTSearch\Stemmer\RussianStemmer::class,
            'charset' => 'utf8'
        ];
        return $config;
    }
}
