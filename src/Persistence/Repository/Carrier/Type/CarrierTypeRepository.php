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

namespace App\Persistence\Repository\Carrier\Type;

use App\Persistence\Entity\Carrier\Type\CarrierType;
use App\Persistence\Entity\NamedEntityInterface;
use App\Persistence\Repository\NamedEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @method CarrierType|null find(int $id, int $lockMode = null, int $lockVersion = null)
 * @method CarrierType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarrierType[]    findAll()
 * @method CarrierType[]    findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 * @method CarrierType|null findOneByName(string $name)
 * @method CarrierType      findOneByNameOrCreate(string $name)
 */
final class CarrierTypeRepository extends NamedEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CarrierType::class);
    }

    protected function createEmpty(): NamedEntityInterface
    {
        return new CarrierType();
    }
}