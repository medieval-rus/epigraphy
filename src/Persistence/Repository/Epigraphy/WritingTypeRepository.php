<?php

declare(strict_types=1);

/*
 * This file is part of «Epigraphy of Medieval Rus'» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus'» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus'» database is distributed
 * in the hope  that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus'» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\Persistence\Repository\Epigraphy;

use App\Persistence\Entity\Epigraphy\NamedEntityInterface;
use App\Persistence\Entity\Epigraphy\WritingType;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @method WritingType|null find(int $id, int $lockMode = null, int $lockVersion = null)
 * @method WritingType|null findOneBy(array $criteria, array $orderBy = null)
 * @method WritingType[]    findAll()
 * @method WritingType[]    findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 * @method WritingType|null findOneByName(string $name)
 * @method WritingType      findOneByNameOrCreate(string $name)
 */
final class WritingTypeRepository extends NamedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WritingType::class);
    }

    protected function createEmpty(): NamedEntityInterface
    {
        return new WritingType();
    }
}