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
    private ActualValueExtractorInterface $extractor;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        InscriptionRepository $inscriptionRepository,
        ActualValueExtractorInterface $extractor
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->inscriptionRepository = $inscriptionRepository;
        $this->extractor = $extractor;
    }

    /**
     * Returns an array of metadata of all the inscriptions, 
     * where each item of the array is an array of the metadata of a single inscription. 
     * 
     * The metadata includes information such as the inscription's ID, 
     * its discovery site, its alphabet, etc.
     * 
     */
    public function getMetadata(string $baseUrl, bool $onlyShownOnSite = false): array
    {
        return array_map(
            fn (Inscription $inscription): array => $this->getMetadataRow($inscription, $baseUrl),
            $this->inscriptionRepository->findAllInConventionalOrder($onlyShownOnSite, true)
        );
    }

    /**
     * Returns an array of texts of all the inscriptions, 
     * where each item of the array is an array of the texts of a single inscription.
     * 
     */
    public function getTexts(bool $onlyShownOnSite = false): array
    {
        return array_map(
            fn (Inscription $inscription): array => $this->getText($inscription),
            $this->inscriptionRepository->findAllInConventionalOrder($onlyShownOnSite, true)
        );
    }

    /**
     * Returns an array of plain formatted texts of all the inscriptions, 
     * where each item of the array is a string containing the plain formatted text 
     * of a single inscription.
     * 
     */
    public function getPlainFormattedTexts(bool $onlyShownOnSite = true): array
    {
        $inscriptions = $this->inscriptionRepository->findAllInConventionalOrder($onlyShownOnSite, true);
        $newTexts = array_map(
            function (Inscription $item): string {
                $textValue = $this->getText($item);
                $text = $textValue['texts'][0]['text'] ?? '';
                $textArray = preg_split(
                    '/\r\n/', $text
                );
                $newText = '';
                foreach ($textArray as $key => $value) {
                    $newText = $newText.preg_replace('/⸗$/', '-', $value)."\n";
                }
                return $this->getPath($item)."\n".substr($newText, 0, strlen($newText) - 1);
            },
            $inscriptions
        );
        
        return $newTexts;
    }

    public function getXmlFormattedTexts(bool $onlyShownOnSite = false): array
    {
        $texts = $this->getTexts($onlyShownOnSite);
        return array_map(
            function (array $item): array {
                $text = $item['texts'][0]['text'] ?? '';
                $textArray = preg_split(
                    '/\r\n/', $text
                );
                $translation = $item['translations'] ? $item['translations'][0] : '';
                $structured_item = [
                    'header' => ['#' => ''],
                    'body' => [
                        'para' => [
                            'se' => [
                                ['@lang' => 'orv', '#' => $textArray], 
                                ['@lang' => 'rus', '#' => $translation]
                            ]
                        ]
                    ],
                ];
                return $structured_item;
            },
            $texts
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

    /**
     *  Returns the metadata of a single inscription as an array
     */
    private function getMetadataRow(Inscription $inscription, string $baseUrl): array
    {
        return [
            'path' => $this->getPath($inscription),
            // 'number' => $inscription->getNumber(),
            'header' => $this->formatDescription(
                $this->extractor->extractFromZeroRowAsStrings($inscription, 'description')[0] ? 
                $this->extractor->extractFromZeroRowAsStrings($inscription, 'description')[0]->getValue() :
                null
            ),
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
                    ->getCarrier()
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
            'created' => $this->formatCreatedAt($inscription->getConventionalDate()),
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

    /**
     * Returns the text of a single inscription as an array.
     */
    private function getText(Inscription $inscription): array
    {
        $alphabet = $inscription->getZeroRow()->getAlphabets()[0];
        $alphabet_name = $alphabet ? $alphabet->getName() : "кириллица";
        $translations = array_values(
            array_filter([
                $inscription->getZeroRow()->getTranslation(),
                ...$inscription->getZeroRow()->getTranslationReferences()->map(
                    fn ($item) => $item->getTranslation()
                )
            ])
        );
        $formattedTranslations = array_map([$this, 'formatTranslation'], $translations);
        $reconstructions = array_values(array_filter([
		$inscription->getZeroRow()->getReconstruction(),
		...$inscription->getZeroRow()->getReconstructionReferences()->map(
			fn ($item) => $item->getReconstruction()
		)
	]));
        if (count($reconstructions) !== 0) {
            $textPropName = 'reconstruction';
        } else {
            if ($alphabet_name === 'глаголица') {
                $textPropName = 'transliteration';
            } else {
                $textPropName = 'text';
            }
        }
        return [
            'number' => $inscription->getId(),
            'texts' => array_map(
                [$this, 'formatTextValue'],
                $this->extractor->extractFromZeroRowAsStrings($inscription, $textPropName)
            ),
            'translations' => $formattedTranslations,
        ];
    }

    public function formatTextValue(StringActualValue $value): array
    {
        $text_value = $value->getValue();
        $newTextValue = str_replace('/im./', '', $text_value);
        $newTextValue = str_replace('|im.|', '', $newTextValue);
        $newTextValue = str_replace('/vac./', '', $newTextValue);
        $newTextValue = str_replace('|vac.|', '', $newTextValue);
        $newTextValue = str_replace('*', '', $newTextValue);
        $newTextValue = str_replace('...', '…', $newTextValue);
        $newTextValue = str_replace('\\', '', $newTextValue);
        $newTextValue = str_replace('оу', 'ѹ', $newTextValue);
        $newTextValue = str_replace('Оу', 'Ѹ', $newTextValue);
        $newTextValue = preg_replace('/⸗(?=\r\n)/', '-', $newTextValue);
        $newTextValue = preg_replace('/(<.+?>\r\n)/', "-comment:$1", $newTextValue);
        return [
            // 'interpretation' => $value->getDescription(),
            'text' => $newTextValue,
        ];
    }

    public function formatCreatedAt(?string $createdAt): ?string
    {
        if ($createdAt === null) {
            return null;
        }
        $newCreatedAt = preg_replace('/[\[\]?→←]/', '', $createdAt);
        $newCreatedAt = preg_replace('/[\/–]/', '-', $newCreatedAt);
        $newCreatedAt = str_replace(';', ',', $newCreatedAt);
        // $newCreatedAt = $createdAt;
        return $newCreatedAt;
    }

    public function formatTranslation(?string $translation): ?string
    {
        if ($translation === null) {
            return $translation;
        }
        $newTranslation = preg_replace("/[‘‛']/", "", $translation);
        return $newTranslation;
    }

    public function formatDescription(?string $description): ?string
    {
        if ($description === null) {
            return $description;
        }
        $descriptionArray = preg_split('/\r{0,1}\n{0,1}<.+?>\r{0,1}\n{0,1} {0,1}/', $description);
        if (count($descriptionArray) > 1 && preg_match('/ {0,1}/', $descriptionArray[0])) {
            array_shift($descriptionArray);
        }

        $newDescription = implode(' | ', $descriptionArray);
        $newDescription = str_replace(';', ',', $newDescription);
        return $newDescription;
    }

    /**
     * Returns the path of a single inscription as a string.
     */
    private function getPath(Inscription $inscription): string {
        $id = (string) $inscription->getId();
        $padded_id = str_pad($id, 5, "0", STR_PAD_LEFT);
        return '/л. '.$padded_id."/\n/д. ".$padded_id.'/';
    }

    private function join(Collection $collection): string
    {
        return implode(' | ', $collection->toArray());
    }
}
