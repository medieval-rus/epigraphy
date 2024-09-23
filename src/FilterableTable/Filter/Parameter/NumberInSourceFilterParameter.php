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

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Alias\AliasFactoryInterface;
use Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Parameter\ParameterFactoryInterface;

final class NumberInSourceFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
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
        return 'number-in-source';
    }

    public function getType(): string
    {
        return TextType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        return [
            'label' => 'controller.inscription.list.filter.numberInSource',
            'attr' => ['data-vyfony-filterable-table-filter-parameter' => true],
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
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('App\Persistence\Entity\Epigraphy\Interpretation', 'i');
        $rsm->addScalarResult('inscription_id', 'inscription_id');

        $query = $queryBuilder->getEntityManager()->createNativeQuery(
            'SELECT inscription_id FROM interpretation WHERE number_in_source = ?', $rsm
        );
        $query->setParameter(1, mb_strtolower($filterValue));
        $ids = $query->getResult();

        if (count($ids) === 0) {
            return (string) $queryBuilder->expr()->in($entityAlias.'.id', ['0']);;
        }

        return (string) $queryBuilder->expr()->in($entityAlias.'.id', array_map(fn($id) => $id['inscription_id'], $ids));
    }
}

