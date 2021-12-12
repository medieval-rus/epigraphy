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

    private function getMetadataRow(Inscription $inscription, string $baseUrl): array
    {
        return [
            'id' => $inscription->getId(),
            'number' => $inscription->getNumber(),
            'header' => sprintf('Надпись %s', $inscription->getId()),
            'subcorp' => 'epigraphy',
            'tagging' => 'manual',
            'carrier_name' => $inscription->getCarrier()->getIndividualName(),
            'carrier_origin1' => $inscription->getCarrier()->getOrigin1(),
            'carrier_origin2' => $inscription->getCarrier()->getOrigin2(),
            'carrier_type' => $this->join(
                $inscription
                    ->getCarrier()
                    ->getTypes()
                    ->map(fn (CarrierType $carrierType): string => $carrierType->getName())
            ),
            'carrier_category' => $this->join(
                $inscription
                    ->getCarrier()
                    ->getCategories()
                    ->map(fn (CarrierCategory $carrierCategory): string => $carrierCategory->getName())
            ),
            'writing_method' => $this->join(
                $inscription
                    ->getZeroRow()
                    ->getWritingMethods()
                    ->map(fn (WritingMethod $writingMethod): string => $writingMethod->getName())
            ),
            'preservation_state' => $this->join(
                $inscription
                    ->getZeroRow()
                    ->getPreservationStates()
                    ->map(fn (PreservationState $preservationState): string => $preservationState->getName())
            ),
            'material' => $this->join(
                $inscription
                    ->getZeroRow()
                    ->getMaterials()
                    ->map(fn (Material $material): string => $material->getName())
            ),
            'content_category' => $this->join(
                $inscription
                    ->getZeroRow()
                    ->getContentCategories()
                    ->map(fn (ContentCategory $contentCategory): string => $contentCategory->getName())
            ),
            'stratigraphical_date' => $inscription->getZeroRow()->getStratigraphicalDate(),
            'non_stratigraphical_date' => $inscription->getZeroRow()->getNonStratigraphicalDate(),
            'conventional_date' => $inscription->getConventionalDate(),
            'description' => $inscription->getZeroRow()->getDescription(),
            'link' => $baseUrl.$this->urlGenerator->generate(
                'inscription__show',
                [
                    'id' => $inscription->getId(),
                ]
            ),
        ];
    }

    private function getText(Inscription $inscription): array
    {
        return [
            'number' => $inscription->getId(),
            'texts' => array_map(
                fn (StringActualValue $value): array => [
                    'interpretation' => $value->getDescription(),
                    'text' => $value->getValue(),
                ],
                $this->actualValueExtractor->extractFromZeroRowAsStrings($inscription, 'text')
            ),
        ];
    }

    private function join(Collection $collection): string
    {
        return implode(' | ', $collection->toArray());
    }
}
