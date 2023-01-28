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
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

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
                $context = [
                    'csv_delimiter' => ',',
                    'csv_end_of_line' => "\r\n",
                    'csv_enclosure' => '"',
                    'csv_escape_char' => '\\',
                ];
                $encoder = new CsvEncoder();
                $content = $encoder->encode($metadata, 'csv', $context);
                $response = new Response($content);
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
     * @Route("/plain-texts/", name="api__v1__corpus__plain__texts", methods={"GET"})
     */
    public function plainTexts(CorpusDataProviderInterface $corpusDataProvider, Request $request): Response
    {
        $texts = $corpusDataProvider->getPlainFormattedTexts(true);

        $joinedTexts = implode("\n", $texts);

        $response = new Response();

        $response->setContent($joinedTexts);

        if ('true' === $request->query->get('as-file')) {
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                sprintf('%s_corpus_texts_%s.txt', $request->getHost(), (new DateTime())->format('Y-m-d-H-i-s'))
            );

            $response->headers->set('Content-Disposition', $disposition);            
        }

        return $response;
    }


    /**
     * @Route("/xml-texts/", name="api__v1__corpus__xml__texts", methods={"GET"})
     */
    public function xmlTexts(CorpusDataProviderInterface $corpusDataProvider, Request $request): Response
    {
        $texts = $corpusDataProvider->getXmlFormattedTexts(true);

        $response = new Response();

        $response->setContent($this->toXml($texts));

        if ('true' === $request->query->get('as-file')) {
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                sprintf('%s_corpus_texts_%s.xml', $request->getHost(), (new DateTime())->format('Y-m-d-H-i-s'))
            );

            $response->headers->set('Content-Disposition', $disposition);            
        }

        return $response;
    }

    /**
     * @Route("/texts/", name="api__v1__corpus__texts", methods={"GET"})
     */
    public function texts(CorpusDataProviderInterface $corpusDataProvider, Request $request): Response
    {
        $texts = $corpusDataProvider->getTexts(true);
        $new_texts = array_map(
            fn (array $item) => ['number' => $item['number'], 'texts' => $item['texts']],
            $texts
        );

        $response = new Response();

        $response->setContent($this->toJson($new_texts));

        if ('true' === $request->query->get('as-file')) {
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                sprintf('%s_corpus_texts_%s.json', $request->getHost(), (new DateTime())->format('Y-m-d-H-i-s'))
            );
            $response->headers->set('Content-Disposition', $disposition);
        }

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

    private function toXml(array $array): string
    {
        $xmlEncoder = new XmlEncoder();
        $context = [
            'xml_root_node_name' => 'html',
            'xml_version' => '1.0',
            'xml_encoding' => 'utf-8',
            XmlEncoder::ENCODER_IGNORED_NODE_TYPES => [\XML_COMMENT_NODE, \XML_CDATA_SECTION_NODE]
        ];

        $xmlItems = array_map(
            function (array $item) use ($xmlEncoder) {
                $encoded_item = $xmlEncoder->encode($item, 'xml', $context);
                return $encoded_item;
            },
            $array
        );
        $xmlString = implode("", $xmlItems);
        $xmlString = preg_replace('/⸗/', '<lbr/>', $xmlString); // kostyl
        $xmlString = preg_replace('/item/', 'line', $xmlString); // kostyl
        $xmlString = preg_replace('/key/', 'id', $xmlString); // kostyl
        return $xmlString;
    }
}
