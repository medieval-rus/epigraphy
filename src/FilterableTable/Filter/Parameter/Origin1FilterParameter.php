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

use App\Persistence\Entity\Epigraphy\Inscription;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Alias\AliasFactoryInterface;

final class Origin1FilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    private AliasFactoryInterface $aliasFactory;

    public function __construct(AliasFactoryInterface $aliasFactory)
    {
        $this->aliasFactory = $aliasFactory;
    }

    public function getQueryParameterName(): string
    {
        return 'origin1';
    }

    public function getType(): string
    {
        return ChoiceType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        $queryBuilder = $entityManager
            ->getRepository(Inscription::class)
            ->createQueryBuilder('inscription')
            ->setParameter(':isShownOnSite', true);

        $choices = array_column(
            $queryBuilder
                ->innerJoin('inscription.carrier', 'carrier')
                ->select('carrier.origin1')
                ->where($queryBuilder->expr()->eq('inscription.isShownOnSite', ':isShownOnSite'))
                ->andWhere($queryBuilder->expr()->isNotNull('carrier.origin1'))
                ->distinct()
                ->getQuery()
                ->getArrayResult(),
            'origin1'
        );

        natsort($choices);

        return [
            'label' => 'controller.inscription.list.filter.origin1',
            'attr' => ['data-vyfony-filterable-table-filter-parameter' => true],
            'choices' => array_combine($choices, $choices),
            'expanded' => false,
            'multiple' => true,
        ];
    }

    /**
     * @param mixed $formData
     */
    public function buildWhereExpression(QueryBuilder $queryBuilder, $formData, string $entityAlias): ?string
    {
        if (0 === \count($formData)) {
            return null;
        }

        $queryBuilder
            ->innerJoin(
                $entityAlias.'.carrier',
                $carrierAlias = $this->aliasFactory->createAlias(static::class, 'carrier')
            )
        ;

        return (string) $queryBuilder->expr()->in($carrierAlias.'.origin1', $formData);
    }
}
