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

namespace App\Services\Corpus;

use App\Models\StringActualValue;
use App\Persistence\Entity\Epigraphy\DiscoverySite;
use App\Persistence\Entity\Epigraphy\Alphabet;
use App\Persistence\Entity\Epigraphy\CarrierCategory;
use App\Persistence\Entity\Epigraphy\CarrierType;
use App\Persistence\Entity\Epigraphy\ContentCategory;
use App\Persistence\Entity\Epigraphy\Inscription;
use App\Persistence\Entity\Epigraphy\Material;
use App\Persistence\Entity\Epigraphy\PreservationState;
use App\Persistence\Entity\Epigraphy\WritingMethod;
use App\Persistence\Repository\Epigraphy\InscriptionRepository;
use App\Services\Epigraphy\ActualValue\Extractor\ActualValueExtractorInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CorpusDataProvider implements CorpusDataProviderInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private InscriptionRepository $inscriptionRepository;
    private ActualValueExtractorInterface $actualValueExtractor;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        InscriptionRepository $inscriptionRepository,
        ActualValueExtractorInterface $actualValueExtractor
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->inscriptionRepository = $inscriptionRepository;
        $this->actualValueExtractor = $actualValueExtractor;
    }

    public function getMetadata(string $baseUrl, bool $onlyShownOnSite = false): array
    {
        return array_map(
            fn (Inscription $inscription): array => $this->getMetadataRow($inscription, $baseUrl),
            $this->inscriptionRepository->findAllInConventionalOrder($onlyShownOnSite, true)
        );
    }

    public function getTexts(bool $onlyShownOnSite = false): array
    {
        return array_map(
            fn (Inscription $inscription): array => $this->getText($inscription),
            $this->inscriptionRepository->findAllInConventionalOrder($onlyShownOnSite, true)
        );
    }

    public function getStatistics(bool $onlyShownOnSite = false): array
    {
        $texts = $this->getTexts($onlyShownOnSite);

        return [
            'documentsCount' => \count($texts),
            'piecesCount' => array_sum(
                array_map(
                    fn (string $text): int => 1 + substr_count(
                        str_replace(
                            "\n",
                            ' ',
                            str_replace(
                                "⸗\n",
                                '',
                                str_replace(
                                    "\r\n",
                                    "\n",
                                    $text
                                )
                            )
                        ),
                        ' '
                    ),
                    array_filter(
                        array_map(
                            function (array $inscriptionTextData): ?string {
                                $textsOfInscription = $inscriptionTextData['texts'];

                                $zeroRowText = current(
                                    array_filter(
                                        $textsOfInscription,
                                        fn (array $textData): bool => null === $textData['interpretation']
                                    )
                                );

                                if (false !== $zeroRowText) {
                                    return $zeroRowText['text'];
                                }

                                if (\count($textsOfInscription) > 0) {
                                    return $textsOfInscription[0]['text'];
                                }

                                return null;
                            },
                            $texts
                        ),
                        fn (?string $text): bool => null !== $text && mb_strlen($text) > 0
                    )
                )
            ),
        ];
    }

    private function getMetadataRow(Inscription $inscription, string $baseUrl): array
    {
        return [
            'path' => $this->getPath($inscription),
            // 'number' => $inscription->getNumber(),
            'header' => $inscription->getZeroRow()->getDescription(),
            'category' => $this->join(
                $inscription
                    ->getZeroRow()
                    ->getContentCategories()
                    ->map(function (ContentCategory $contentCategory): string {
                        $super = $contentCategory->getSuperCategory();
                        return $super ? $super->getName() : $contentCategory->getName();
                    })
            ),
            'genre' => $this->join(
                $inscription
                    ->getZeroRow()
                    ->getContentCategories()
                    ->map(fn (ContentCategory $contentCategory): string => $contentCategory->getName())
            ),
            'alphabet' => $this->join(
                $inscription
                    ->getZeroRow()
                    ->getAlphabets()
                    ->map(function (Alphabet $alphabet): string {
                        return $alphabet->getName();
                    })
            ),
            'town' => $this->join(
                $inscription
                    ->getCarrier()
                    ->getDiscoverySite()
                    ->map(function (DiscoverySite $discoverySite): string {
                        if (count($discoverySite->getCities()) != 0) {
                            return $discoverySite->getCities()[0]->getName(); 
                        }
                        return '';
                    })
            ),
            'carrier' => $inscription->getCarrier()->getIndividualName(),
            'cat_carrier' => $this->join(
                $inscription
                    ->getCarrier()
                    ->getCategories()
                    ->map(function (CarrierCategory $carrierCategory): string {
                        $super = $carrierCategory->getSuperCategory();
                        return $super ? $super->getName() : $carrierCategory->getName();
                    })
            ),
            'material' => $this->join(
                $inscription
                    ->getZeroRow()
                    ->getMaterials()
                    ->map(function (Material $material): string {
                        $super = $material->getSuperMaterial();
                        return $super ? $super->getName() : $material->getName();
                    })
            ),
            'technique' => $this->join(
                $inscription
                    ->getZeroRow()
                    ->getWritingMethods()
                    ->map(function (WritingMethod $writingMethod): string {
                        $super = $writingMethod->getSuperMethod();
                        return $super ? $super->getName() : $writingMethod->getName();
                    })
            ),
            'state_of_preservation' => $this->join(
                $inscription
                    ->getZeroRow()
                    ->getPreservationStates()
                    ->map(fn (PreservationState $preservationState): string => $preservationState->getName())
            ),
            'created' => $inscription->getConventionalDate(),
            'subcorp' => 'epigraphica',
            'tagging' => 'manual',
            'link' => $baseUrl.$this->urlGenerator->generate(
                'inscription__show',
                [
                    'id' => $inscription->getId(),
                ]
            ),
            '__num__' => $inscription->getId(),
        ];
    }

    private function getText(Inscription $inscription): array
    {
        $alphabet = $inscription->getZeroRow()->getAlphabets()[0];
        $alphabet_name = $alphabet ? $alphabet->getName() : "кириллица";
        switch ($alphabet_name) {
            case 'глаголица':
                return [
                    'number' => $inscription->getId(),
                    'texts' => array_map(
                        [$this, 'formatTextValue'],
                        $this->actualValueExtractor->extractFromZeroRowAsStrings($inscription, 'transliteration')
                    ),
                ];
                break;
            default:
                return [
                    'number' => $inscription->getId(),
                    'texts' => array_map(
                        [$this, 'formatTextValue'],
                        $this->actualValueExtractor->extractFromZeroRowAsStrings($inscription, 'text')
                    ),
                ];
                break;
        }
    }

    public function formatTextValue(StringActualValue $value): array {
        $text_value = $value->getValue();
        $new_text_value = str_replace('/im./', '', $text_value);
        $new_text_value = str_replace('оу', 'ѹ', $new_text_value);
        $new_text_value = str_replace('Оу', 'Ѹ', $new_text_value);
        return [
            // 'interpretation' => $value->getDescription(),
            'text' => $new_text_value,
        ];
    }

    private function getPath(Inscription $inscription): string {
        $id = (string) $inscription->getId();
        return str_pad($id, 5, "0", STR_PAD_LEFT);
    }

    private function join(Collection $collection): string
    {
        return implode(' | ', $collection->toArray());
    }
}
