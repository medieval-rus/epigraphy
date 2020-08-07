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

namespace App\FilterableTable\Filter\Parameter;

use App\Persistence\Entity\Epigraphy\WritingType;
use App\Persistence\Repository\Epigraphy\WritingTypeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Alias\AliasFactoryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
class WritingTypeFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    /**
     * @var AliasFactoryInterface
     */
    private $aliasFactory;

    public function __construct(AliasFactoryInterface $aliasFactory)
    {
        $this->aliasFactory = $aliasFactory;
    }

    public function getQueryParameterName(): string
    {
        return 'writing-type';
    }

    public function getType(): string
    {
        return EntityType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        return [
            'label' => 'controller.inscription.list.filter.writingType',
            'attr' => [
                'class' => '',
                'data-vyfony-filterable-table-filter-parameter' => true,
            ],
            'class' => WritingType::class,
            'choice_label' => 'name',
            'expanded' => false,
            'multiple' => true,
            'query_builder' => $this->createQueryBuilder(),
        ];
    }

    /**
     * @param mixed $formData
     */
    public function buildWhereExpression(QueryBuilder $queryBuilder, $formData, string $entityAlias): ?string
    {
        $writingTypes = $formData;

        if (0 === \count($writingTypes)) {
            return null;
        }

        $ids = [];

        foreach ($writingTypes as $writingType) {
            $ids[] = $writingType->getId();
        }
        $queryBuilder
            ->innerJoin(
                $entityAlias.'.zeroRow',
                $zeroRowAlias = $this->aliasFactory->createAlias(static::class, 'zero_row')
            )
            ->leftJoin(
                $zeroRowAlias.'.writingTypeReferences',
                $interpretationsAlias = $this->aliasFactory->createAlias(static::class, 'references')
            )
            ->leftJoin(
                $zeroRowAlias.'.writingType',
                $writingTypeOfZeroRowAlias = $this->createAlias()
            )
            ->leftJoin(
                $interpretationsAlias.'.writingType',
                $writingTypeOfInterpretationAlias = $this->createAlias()
            )
        ;

        return (string) $queryBuilder->expr()->orX(
            $queryBuilder->expr()->in($writingTypeOfZeroRowAlias.'.id', $ids),
            $queryBuilder->expr()->in($writingTypeOfInterpretationAlias.'.id', $ids)
        );
    }

    private function createQueryBuilder(): callable
    {
        return function (WritingTypeRepository $repository): QueryBuilder {
            $entityAlias = $this->createAlias();

            return $repository
                ->createQueryBuilder($entityAlias)
                ->orderBy($entityAlias.'.name', 'ASC');
        };
    }

    private function createAlias(): string
    {
        return $this->aliasFactory->createAlias(static::class, 'writing_type');
    }
}
