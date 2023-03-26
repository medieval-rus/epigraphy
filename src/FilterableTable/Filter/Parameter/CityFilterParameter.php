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

use App\Persistence\Entity\Epigraphy\City;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Alias\AliasFactoryInterface;

final class CityFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    private AliasFactoryInterface $aliasFactory;

    public function __construct(AliasFactoryInterface $aliasFactory)
    {
        $this->aliasFactory = $aliasFactory;
    }

    public function getQueryParameterName(): string
    {
        return 'city';
    }

    public function getType(): string
    {
        return EntityType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        return [
            'label' => 'controller.inscription.list.filter.city',
            'attr' => ['data-vyfony-filterable-table-filter-parameter' => true],
            'class' => City::class,
            'choice_label' => 'name',
            'expanded' => false,
            'multiple' => false,
            'required' => false,
            'query_builder' => function (EntityRepository $repository): QueryBuilder {
                $entityAlias = $this->createAlias();

                return $repository
                    ->createQueryBuilder($entityAlias)
                    ->orderBy($entityAlias.'.name', 'ASC');
            },
        ];
    }

    /**
     * @param mixed $formData
     */
    public function buildWhereExpression(QueryBuilder $queryBuilder, $formData, string $entityAlias): ?string
    {
        if (is_null($formData)) {
            return null;
        }

        $ids = [$formData->getId()];

        
        $queryBuilder
            ->innerJoin(
                $entityAlias.'.carrier',
                $carrierAlias = $this->aliasFactory->createAlias(static::class, 'carrier')
            )
            ->leftJoin(
                $carrierAlias.'.discoverySite',
                $carrierDiscoverySiteAlias = $this->aliasFactory->createAlias(static::class, 'discoverySite')
            )
            ->leftJoin(
                $carrierAlias.'.storageSite',
                $carrierStorageSiteAlias = $this->aliasFactory->createAlias(static::class, 'storageSite')
            )
            ->leftJoin(
                $carrierDiscoverySiteAlias.'.cities',
                $discoverySiteCityAlias = $this->createAlias()
            )
            ->leftJoin(
                $carrierStorageSiteAlias.'.cities',
                $storageSiteCityAlias = $this->createAlias()
            )
        ;

        return (string) $queryBuilder->expr()->orX(
            $queryBuilder->expr()->in($storageSiteCityAlias.'.id', $ids),
            $queryBuilder->expr()->in($discoverySiteCityAlias.'.id', $ids)
        );
    }

    private function createAlias(): string
    {
        return $this->aliasFactory->createAlias(static::class, 'cities');
    }
}
