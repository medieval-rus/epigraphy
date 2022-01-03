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

namespace App\Api\V1;

use App\Services\Corpus\CorpusDataProviderInterface;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/corpus")
 */
final class CorpusController extends AbstractController
{
    /**
     * @Route("/metadata/", name="api__v1__corpus__metadata", methods={"GET"})
     */
    public function metadata(CorpusDataProviderInterface $corpusDataProvider, Request $request): Response
    {
        $metadata = $corpusDataProvider->getMetadata($request->getSchemeAndHttpHost(), true);

        if ('true' === $request->query->get('csv')) {
            if (\count($metadata) > 0) {
                array_unshift($metadata, array_keys($metadata[0]));

                $columnSeparator = ';';

                $response = new Response(
                    implode(
                        "\r\n",
                        array_map(
                            fn (array $row): string => implode(
                                $columnSeparator,
                                array_map(
                                    fn (?string $cell): string => str_replace($columnSeparator, ',', ($cell ?? '')),
                                    $row
                                )
                            ),
                            $metadata
                        )
                    )
                );

                $disposition = $response->headers->makeDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    sprintf('%s_corpus_metadata_%s.csv', $request->getHost(), (new DateTime())->format('Y-m-d-H-i-s'))
                );

                $response->headers->set('Content-Disposition', $disposition);

                return $response;
            }
        }

        return new Response($this->toJson($metadata));
    }

    /**
     * @Route("/texts/", name="api__v1__corpus__texts", methods={"GET"})
     */
    public function texts(CorpusDataProviderInterface $corpusDataProvider): Response
    {
        $texts = $corpusDataProvider->getTexts(true);

        $response = new Response();

        $response->setContent($this->toJson($texts));

        return $response;
    }

    /**
     * @Route("/statistics/", name="api__v1__corpus__statistics", methods={"GET"})
     */
    public function statistics(CorpusDataProviderInterface $corpusDataProvider): Response
    {
        $statistics = $corpusDataProvider->getStatistics(true);

        $response = new Response();

        $response->setContent($this->toJson($statistics));

        return $response;
    }

    private function toJson(array $array): string
    {
        return json_encode($array, \JSON_UNESCAPED_UNICODE | \JSON_PRETTY_PRINT);
    }
}
