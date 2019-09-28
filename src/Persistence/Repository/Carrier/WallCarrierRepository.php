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
use App\Persistence\Repository\Building\BuildingRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use LogicException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @method WallCarrier|null find(int $id, int $lockMode = null, int $lockVersion = null)
 * @method WallCarrier|null findOneBy(array $criteria, array $orderBy = null)
 * @method WallCarrier[]    findAll()
 * @method WallCarrier[]    findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 */
final class WallCarrierRepository extends ServiceEntityRepository
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
     * @param string      $buildingTypeName
     * @param string|null $buildingName
     *
     * @throws ORMException
     *
     * @return WallCarrier
     */
    public function findOneOrCreate(string $buildingTypeName, ?string  $buildingName): WallCarrier
    {
        $carrier = $this->findOne($buildingTypeName, $buildingName);

        if (null !== $carrier) {
            return $carrier;
        }

        return $this->create($buildingTypeName, $buildingName);
    }

    /**
     * @param string      $buildingTypeName
     * @param string|null $buildingName
     *
     * @return WallCarrier
     */
    private function findOne(string $buildingTypeName, ?string  $buildingName): WallCarrier
    {
        $queryBuilder = $this->createQueryBuilder('wallCarrier');

        $queryBuilder
            ->innerJoin('wallCarrier.building', 'building')
            ->innerJoin('building.type', 'buildingType')
            ->setParameter('buildingTypeName', $buildingTypeName)
            ->select('wallCarrier')
            ->andWhere($queryBuilder->expr()->eq('buildingType.name', ':buildingTypeName'))
        ;

        if (null !== $buildingName) {
            $queryBuilder
                ->setParameter('buildingName', $buildingName)
                ->andWhere($queryBuilder->expr()->eq('building.name', ':buildingName'));
        }

        $query = $queryBuilder->getQuery();

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new LogicException(sprintf('The built query "%s" returns non-unique result', $query->getDQL()));
        }
    }

    /**
     * @param string      $buildingTypeName
     * @param string|null $buildingName
     *
     * @throws ORMException
     *
     * @return WallCarrier
     */
    private function create(string $buildingTypeName, ?string $buildingName): WallCarrier
    {
        $carrier = new WallCarrier();

        $carrier->setBuilding($this->buildingRepository->findOneOrCreate($buildingTypeName, $buildingName));

        $this->getEntityManager()->persist($carrier);

        return $carrier;
    }
}
