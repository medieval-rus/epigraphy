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

namespace App\Services\Corpus\Morphy;

use App\Repository\Document\DocumentRepository;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use App\Services\Corpus\Morphy\Models\Xhtml\XhtmlFile;
use App\Services\Corpus\Morphy\MorphyParserInterface;

final class MorphyParser implements MorphyParserInterface
{
    public function __construct() {
        $this->xmlSerializer = new Serializer(
            [
                new ArrayDenormalizer(),
                new ObjectNormalizer(null, null, null, new ReflectionExtractor())
            ],
            [new XmlEncoder()]
        );
    }

    public function parseXhtml(string $rawXhtml)
    {
        $result = $this->xmlSerializer->deserialize($rawXhtml, XhtmlFile::class, 'xml');
        return $result;
    }

    public function unparseXhtml(array $arr): string
    {
        $result = $this->xmlSerializer->serialize(
            $arr,
            'xml',
            [
                'xml_format_output' => true,
                // 'as_collection' => true,
                'xml_root_node_name' => 'html',
                'xml_version' => '1.0',
                XmlEncoder::ENCODING => 'UTF-8',
                XmlEncoder::ENCODER_IGNORED_NODE_TYPES => [\XML_COMMENT_NODE, \XML_CDATA_SECTION_NODE]
            ]
        );
        return $result;
    }
}