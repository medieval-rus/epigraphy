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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Alias\AliasFactoryInterface;
use Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Parameter\ParameterFactoryInterface;

final class TextFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    private ParameterFactoryInterface $parameterFactory;
    private AliasFactoryInterface $aliasFactory;

    public function __construct(ParameterFactoryInterface $parameterFactory, AliasFactoryInterface $aliasFactory)
    {
        $this->parameterFactory = $parameterFactory;
        $this->aliasFactory = $aliasFactory;
    }

    public function getQueryParameterName(): string
    {
        return 'text';
    }

    public function getType(): string
    {
        return TextType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        return [
            'label' => 'controller.inscription.list.filter.text',
            'attr' => [
                'data-vyfony-filterable-table-filter-parameter' => true,
                'data-important' => true
            ],
        ];
    }

    /**
     * @param mixed $formData
     */
    public function buildWhereExpression(QueryBuilder $queryBuilder, $formData, string $entityAlias): ?string
    {
        $filterValue = $formData;

        if (null === $filterValue) {
            return null;
        }

        $queryBuilder
            ->innerJoin(
                $entityAlias.'.zeroRow',
                $zeroRowAlias = $this->aliasFactory->createAlias(static::class, 'zero_row')
            )
            ->leftJoin(
                $zeroRowAlias.'.textReferences',
                $interpretationsAlias = $this->aliasFactory->createAlias(static::class, 'references')
            )
        ;

        $queryBuilder->setParameter(
            $parameterName = $this->parameterFactory->createParameter($entityAlias.'_text', 0),
            '%'.mb_strtolower($filterValue).'%'
        );

        return (string) $queryBuilder->expr()->orX(
            $queryBuilder->expr()->like('LOWER('.$zeroRowAlias.'.text)', $parameterName),
            $queryBuilder->expr()->like('LOWER('.$interpretationsAlias.'.text)', $parameterName)
        );
    }
}
