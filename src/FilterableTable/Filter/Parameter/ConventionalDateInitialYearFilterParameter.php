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

namespace App\FilterableTable\Filter\Parameter;

use App\Persistence\Repository\Epigraphy\InscriptionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RequestStack;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;

final class ConventionalDateInitialYearFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    private InscriptionRepository $inscriptionRepository;
    private RequestStack $requestStack;

    public function __construct(
        InscriptionRepository $inscriptionRepository,
        RequestStack $requestStack
    ) {
        $this->inscriptionRepository = $inscriptionRepository;
        $this->requestStack = $requestStack;
    }

    public function getQueryParameterName(): string
    {
        return 'conventionalDateInitialYear';
    }

    public function getType(): string
    {
        return HiddenType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        return [
            'data' => $this->inscriptionRepository->getMinimalConventionalDate(),
        ];
    }

    /**
     * @param mixed $formData
     */
    public function buildWhereExpression(QueryBuilder $queryBuilder, $formData, string $entityAlias): ?string
    {
        if (null === $formData) {
            return null;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        $conventionalDateInitialYear = (int) $formData;
        $conventionalDateFinalYear = (int) $request->query->get('conventionalDateFinalYear', $conventionalDateInitialYear);
        
        // When checkbox is checked, it sends value="1", when unchecked it sends nothing
        $exactMatchParam = $request->query->get('conventionalDateExactMatch');
        $isExactMatch = $exactMatchParam === '1' || $exactMatchParam === 1;

        // Use application-side filtering for robust parsing of diverse date formats
        $entityManager = $queryBuilder->getEntityManager();
        $connection = $entityManager->getConnection();

        // Fetch all candidate inscriptions (ids and dates)
        $sql = 'SELECT id, conventional_date FROM inscription WHERE is_shown_on_site = 1 AND conventional_date IS NOT NULL';
        $rows = $connection->executeQuery($sql)->fetchAllAssociative();

        $matchingIds = [];

        foreach ($rows as $row) {
            $raw = (string) $row['conventional_date'];
            $clean = mb_strtolower($raw);
            // Remove textual prefixes and brackets (keep original separators)
            $clean = preg_replace('/^\s*(після\s*|после\s*)/u', '', $clean);
            $clean = str_replace(['[', ']'], '', $clean);

            // Extract 3-4 digit year tokens robustly (handles en-dash, spaces, concatenations)
            $matches = [];
            preg_match_all('/\d{3,4}/u', $clean, $matches);

            $startYear = null;
            $endYear = null;

            if (!empty($matches[0])) {
                // Use the first two tokens in order if available, otherwise single token
                $first = isset($matches[0][0]) ? (int) $matches[0][0] : null;
                $second = isset($matches[0][1]) ? (int) $matches[0][1] : null;

                if (null !== $first && $first > 0) {
                    $startYear = $first;
                    $endYear = $first;
                }

                if (null !== $second && $second > 0) {
                    $endYear = $second;
                }
            }

            if (null === $startYear || null === $endYear) {
                continue; // skip invalid
            }

            // Discard obviously invalid intervals (end before start)
            if ($endYear < $startYear) {
                continue;
            }

            $isMatch = false;
            if ($startYear === $endYear) {
                // Exact year: inclusive bounds in both modes
                $isMatch = ($startYear >= $conventionalDateInitialYear) && ($endYear <= $conventionalDateFinalYear);
            } else {
                if ($isExactMatch) {
                    // Strict: fully inside filter, exclude borders per spec
                    $isMatch = ($startYear > $conventionalDateInitialYear) && ($endYear < $conventionalDateFinalYear);
                } else {
                    // Non-strict (per spec): an endpoint must lie strictly inside the filter range
                    // Note: intervals that completely cover the filter (both endpoints outside) do NOT match
                    $isMatch = (
                        ($startYear > $conventionalDateInitialYear && $startYear < $conventionalDateFinalYear) ||
                        ($endYear > $conventionalDateInitialYear && $endYear < $conventionalDateFinalYear)
                    );
                }
            }

            if ($isMatch) {
                $matchingIds[] = (int) $row['id'];
            }
        }

        // If no matches, return a condition that will never match
        if (empty($matchingIds)) {
            return $entityAlias . '.id = 0';
        }
        
        // Return the IN condition with matching IDs as a string
        return (string) $queryBuilder->expr()->in($entityAlias . '.id', $matchingIds);
    }
}

