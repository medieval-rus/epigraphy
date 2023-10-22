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

use DateTime;
use App\Form\CorpusXhtmlFormType;
use App\Persistence\Repository\Epigraphy\InscriptionRepository;
use App\Services\Corpus\Morphy\MorphyParserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Epigraphy\ActualValue\Extractor\ActualValueExtractorInterface;

function renameArr($arr) {
    foreach($arr as $key=>$val) {
        if(is_array($val)) {
            if(isset($val['#text'])) {
                $tmp = $val['#text'];
                $val['#'] = $tmp;
                unset($val['#text']);
            }
            $arr[$key] = renameArr($val);
        }
    }
    return $arr;
}

/**
 * Decode HTML entities, including numeric.
 */
function html_numeric_decode(string $str)
{
    $result = preg_replace_callback(
        "/(&#[0-9]+;)/",
        function($m) {
            return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
        },
        html_entity_decode($str)
    );
    return $result;
}

/**
 * @Route("/v1/parallel")
 */
final class ParallelCorpusController extends AbstractController
{
    /**
     * @Route("/parallelize/", name="api__v1__corpus__parallelize", methods={"GET", "POST"})
     */
    public function parallelize(
        Request $request,
        MorphyParserInterface $morphyParser,
        InscriptionRepository $inscriptionRepository,
        ActualValueExtractorInterface $extractor
    ): Response {
        $form = $this->createForm(CorpusXhtmlFormType::class);
        $form->handleRequest($request);
        $isSuccessful = $form->isSubmitted() && $form->isValid();
        if (!$isSuccessful) {
            return $this->renderForm(
                'site/corpus/form.html.twig',
                [
                    'form' => $form,
                    'translationContext' => 'controller.index.index',
                    'assetsContext' => 'index/index'
                ]
            );
        }
        $xhtmlFile = $form->get('xhtml')->getData();
        $xhtmlFileName = $xhtmlFile->getClientOriginalName();
        // won't work with <lbr/> as xml tag
        $xhtmlDocuments = $morphyParser->parseXhtml(
            preg_replace(
                ['/<lbr\/>/', '/(\n)(\W+)(\n)/'],
                ['$', '\1<w>\2</w>\3'], 
                $xhtmlFile->getContent()
            )
        );
        // map ids to translations
        $allDocuments = array_map(
            function ($inscription) use ($extractor) {
                $translation = count($extractor->extractFromZeroRowAsStrings($inscription, 'translation')) > 0 ? 
                $extractor->extractFromZeroRowAsStrings($inscription, 'translation')[0]->getValue() :
                null;
                $translation = $translation ?
                strip_tags(html_numeric_decode($translation)) :
                null;
                return [
                    'path' => 'inscr_' . str_pad((string)$inscription->getId(), 5, "0", STR_PAD_LEFT),
                    'translation' => $translation
                ];
            },
            $inscriptionRepository->findAll()
        );
        $translationsByNumber = array_combine(
            array_column($allDocuments, 'path'),
            array_column($allDocuments, 'translation')
        );
        $newXhtmlDocuments = [
            'head' => ['#' => ''],
            'body' => []
        ];
        $newXhtmlDocuments['body']['document'] = array_map(
            function ($document) use ($translationsByNumber) {
                // throw new Exception();
                $id = $document['@id'];
                $translation = $translationsByNumber[$id];
                
                $newDocument = [
                    '@id' => $id,
                    'para' => [
                        'se' => [['@lang' => 'orv', 'page' => $document['page']], ['@lang' => 'rus', '#' => $translation]]
                    ]
                ];
                return $newDocument;
            },
            $xhtmlDocuments->body['document']
        );
        $newXhtmlDocuments = renameArr($newXhtmlDocuments);
        $rawXml = $morphyParser->unparseXhtml($newXhtmlDocuments);
        $xml = $this->preprocess($rawXml);
        $response = new Response();
        $response->setContent($xml);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('%s_aligned_%s.xml', $request->getHost(), (new DateTime())->format('Y-m-d-H-i-s'))
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    private function preprocess(?string $rawXml)
    {
        $xml = preg_replace(
            ['/\$/', '/<w>(\W+)<\/w>/'],
            ['<lbr/>', '\1'],
            $rawXml
        );
        $xml = preg_replace(
            ['/\n.+?<item[\w\W\n\r]+?<\/item>/', '/\n(\n)/'],
            ['', '\1'],
            $xml
        );
        return $xml;
    }
}