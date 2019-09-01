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

namespace App\Persistence\Repository\Carrier;

use App\Persistence\Entity\Carrier\WallCarrier;
use App\Persistence\Entity\NamedEntityInterface;
use App\Persistence\Repository\Building\BuildingRepository;
use App\Persistence\Repository\NamedEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @method WallCarrier|null find(int $id, int $lockMode = null, int $lockVersion = null)
 * @method WallCarrier|null findOneBy(array $criteria, array $orderBy = null)
 * @method WallCarrier[]    findAll()
 * @method WallCarrier[]    findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 * @method WallCarrier|null findOneByName(string $name)
 * @method WallCarrier      findOneByNameOrCreate(string $name)
 */
final class WallCarrierRepository extends NamedEntityRepository
{
    /**
     * @var BuildingRepository
     */
    private $buildingRepository;

    /**
     * @param RegistryInterface  $registry
     * @param BuildingRepository $buildingRepository
     */
    public function __construct(RegistryInterface $registry, BuildingRepository $buildingRepository)
    {
        parent::__construct($registry, WallCarrier::class);
        $this->buildingRepository = $buildingRepository;
    }

    /**
     * @param string      $name
     * @param string      $buildingTypeName
     * @param string|null $buildingName
     *
     * @throws ORMException
     *
     * @return WallCarrier
     */
    public function findOneOrCreate(string $name, string $buildingTypeName, ?string  $buildingName): WallCarrier
    {
        $carrier = $this->findOneBy(['name' => $name]);

        if (null === $carrier) {
            $carrier = $this->create($name, $buildingTypeName, $buildingName);
        }

        return $carrier;
    }

    /**
     * @return NamedEntityInterface
     */
    protected function createEmpty(): NamedEntityInterface
    {
        return new WallCarrier();
    }

    /**
     * @param string      $name
     * @param string      $buildingTypeName
     * @param string|null $buildingName
     *
     * @throws ORMException
     *
     * @return WallCarrier
     */
    private function create(string $name, string $buildingTypeName, ?string $buildingName): WallCarrier
    {
        $carrier = new WallCarrier();

        $carrier->setName($name);
        $carrier->setBuilding($this->buildingRepository->findOneOrCreate($buildingTypeName, $buildingName));

        $this->getEntityManager()->persist($carrier);

        return $carrier;
    }
}
