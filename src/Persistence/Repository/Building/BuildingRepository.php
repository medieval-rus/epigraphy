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

namespace App\Persistence\Repository\Building;

use App\Persistence\Entity\Building\Building;
use App\Persistence\Repository\Building\Type\BuildingTypeRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @method Building|null find(int $id, int $lockMode = null, int $lockVersion = null)
 * @method Building|null findOneBy(array $criteria, array $orderBy = null)
 * @method Building[]    findAll()
 * @method Building[]    findBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null)
 */
final class BuildingRepository extends ServiceEntityRepository
{
    private const UNKNOWN_BUILDING_NAME = 'неизвестное здание';

    /**
     * @var BuildingTypeRepository
     */
    private $buildingTypeRepository;

    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry, BuildingTypeRepository $buildingTypeRepository)
    {
        parent::__construct($registry, Building::class);
        $this->buildingTypeRepository = $buildingTypeRepository;
    }

    /**
     * @param string      $buildingTypeName
     * @param string|null $buildingName
     *
     * @throws ORMException
     *
     * @return Building
     */
    public function findOneOrCreate(string $buildingTypeName, ?string $buildingName): Building
    {
        if (null === $buildingName) {
            return $this->create(self::UNKNOWN_BUILDING_NAME, $buildingTypeName);
        }

        $building = $this->findOneBy(['name' => $buildingName]);

        if (null !== $building) {
            if ($building->getBuildingType()->getName() !== $buildingTypeName) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Found building with name "%s" and type "%s", while looking for "%s" and "%s"',
                        $building->getName(),
                        $building->getBuildingType()->getName(),
                        $buildingName,
                        $buildingTypeName
                    )
                );
            }

            return $building;
        }

        return $this->create($buildingName, $buildingTypeName);
    }

    /**
     * @param string $buildingName
     * @param string $buildingTypeName
     *
     * @throws ORMException
     *
     * @return Building
     */
    private function create(string $buildingName, string $buildingTypeName): Building
    {
        $building = new Building();

        $building->setName($buildingName);
        $building->setBuildingType($this->buildingTypeRepository->findOneByNameOrCreate($buildingTypeName));

        $this->getEntityManager()->persist($building);

        return $building;
    }
}
