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
        $indexer->query(<<<SQL
SELECT
  inscription.id,
    CONCAT_WS(' ',
    inscription.comment,
    inscription.date_explanation,
    zero_row.place_on_carrier,
    zero_row.text,
    zero_row.translation,
    zero_row.transliteration,
    zero_row.description,
    zero_row.date_in_text,
    zero_row.non_stratigraphical_date,
    zero_row.reconstruction,
    zero_row.normalization,
    zero_row.interpretation_comment,
    zero_row.origin,
    carrier.find_circumstances,
    carrier.characteristics,
    carrier.individual_name,
    carrier.stratigraphical_date,
    carrier.material_description,
    carrier.carrier_history,
    carrier.storage_localization,
    interpretation_content.content,
    localized_inscription.content,
    localized_carrier.content,
    localized_zero_row.content,
    localized_interpretation.content
  ) AS content
FROM inscription
LEFT JOIN carrier ON inscription.carrier_id = carrier.id
LEFT JOIN zero_row ON inscription.zero_row_id = zero_row.id
LEFT JOIN (
    SELECT i.inscription_id,
           GROUP_CONCAT(
               CONCAT_WS(' ',
                   i.comment,
                   i.place_on_carrier,
                   i.text,
                   i.translation,
                   i.transliteration,
                   i.description,
                   i.date_in_text,
                   i.non_stratigraphical_date,
                   i.reconstruction,
                   i.normalization,
                   i.interpretation_comment,
                   i.origin
               )
               SEPARATOR ' '
           ) AS content
    FROM interpretation i
    GROUP BY i.inscription_id
) interpretation_content ON interpretation_content.inscription_id = inscription.id
LEFT JOIN (
    SELECT target_id, GROUP_CONCAT(value SEPARATOR ' ') AS content
    FROM localized_text
    WHERE target_type = 'inscription' AND locale = 'en'
    GROUP BY target_id
) localized_inscription ON localized_inscription.target_id = inscription.id
LEFT JOIN (
    SELECT target_id, GROUP_CONCAT(value SEPARATOR ' ') AS content
    FROM localized_text
    WHERE target_type = 'carrier' AND locale = 'en'
    GROUP BY target_id
) localized_carrier ON localized_carrier.target_id = carrier.id
LEFT JOIN (
    SELECT target_id, GROUP_CONCAT(value SEPARATOR ' ') AS content
    FROM localized_text
    WHERE target_type = 'zero_row' AND locale = 'en'
    GROUP BY target_id
) localized_zero_row ON localized_zero_row.target_id = zero_row.id
LEFT JOIN (
    SELECT i.inscription_id, GROUP_CONCAT(lt.value SEPARATOR ' ') AS content
    FROM localized_text lt
    INNER JOIN interpretation i ON i.id = lt.target_id
    WHERE lt.target_type = 'interpretation' AND lt.locale = 'en'
    GROUP BY i.inscription_id
) localized_interpretation ON localized_interpretation.inscription_id = inscription.id
SQL);
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
