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

namespace App\Persistence\Repository\Epigraphy;

use App\Persistence\Entity\Epigraphy\Inscription;
use App\Services\Epigraphy\Sorting\InscriptionComparerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class InscriptionRepository extends ServiceEntityRepository
{
    private InscriptionComparerInterface $inscriptionComparer;

    public function __construct(ManagerRegistry $registry, InscriptionComparerInterface $inscriptionComparer)
    {
        parent::__construct($registry, Inscription::class);

        $this->inscriptionComparer = $inscriptionComparer;
    }

    /**
     * @return Inscription[]
     */
    public function findAllInConventionalOrder(bool $onlyShownOnSite = false, bool $onlyPartOfCorpus = false): array
    {
        $criteria = [];

        if ($onlyShownOnSite) {
            $criteria['isShownOnSite'] = true;
        }

        if ($onlyPartOfCorpus) {
            $criteria['isPartOfCorpus'] = true;
        }

        $inscriptions = $this->findBy($criteria);

        usort($inscriptions, fn (Inscription $a, Inscription $b): int => $this->inscriptionComparer->compare($a, $b));

        return $inscriptions;
    }
}
