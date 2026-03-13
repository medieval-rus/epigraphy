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

use App\Persistence\Entity\Epigraphy\CarrierCategory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\RequestStack;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Alias\AliasFactoryInterface;

final class SuperCarrierCategoryFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    private AliasFactoryInterface $aliasFactory;
    private RequestStack $requestStack;

    public function __construct(AliasFactoryInterface $aliasFactory, RequestStack $requestStack)
    {
        $this->aliasFactory = $aliasFactory;
        $this->requestStack = $requestStack;
    }

    public function getQueryParameterName(): string
    {
        return 'super-carrier-category';
    }

    public function getType(): string
    {
        return EntityType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $locale = null !== $request ? $request->getLocale() : 'ru';

        return [
            'label' => 'controller.inscription.list.filter.superCarrierCategory',
            'attr' => ['data-vyfony-filterable-table-filter-parameter' => true],
            'class' => CarrierCategory::class,
            'choice_label' => static fn (?CarrierCategory $choice): string => null === $choice
                ? ''
                : (string) $choice->getTranslatedName($locale),
            'expanded' => false,
            'multiple' => false,
            'required'   => false,
            'query_builder' => function (EntityRepository $repository): QueryBuilder {
                $entityAlias = $this->createAlias();

                return $repository
                    ->createQueryBuilder($entityAlias)
                    ->where($entityAlias.'.isSuperCategory = true')
                    ->orderBy($entityAlias.'.name', 'ASC');
            },
        ];
    }

    /**
     * @param mixed $formData
     */
    public function buildWhereExpression(QueryBuilder $queryBuilder, $formData, string $entityAlias): ?string
    {
        // if (0 === \count($formData)) {
        //     return null;
        // }
        if (is_null($formData)) {
            return null;
        }

        // $objs = $formData->map(fn (CarrierCategory $entity): array => $entity->getSubcategories()->toArray())->toArray();
        // $ready_objs = array_merge(...$objs);
        // $ids = array_map(fn (CarrierCategory $entity): int => $entity->getId(), $ready_objs);
        $ready_objs = $formData->getSubcategories()->toArray();
        $ids = array_map(fn (CarrierCategory $entity): int => $entity->getId(), $ready_objs);
        array_push($ids, $formData->getId());

        $queryBuilder
            ->innerJoin(
                $entityAlias.'.carrier',
                $carrierAlias = $this->aliasFactory->createAlias(static::class, 'carrier')
            )
            ->innerJoin(
                $carrierAlias.'.categories',
                $carrierCategoryAlias = $this->createAlias()
            )
        ;

        return (string) $queryBuilder->expr()->in($carrierCategoryAlias.'.id', $ids);
    }

    private function createAlias(): string
    {
        return $this->aliasFactory->createAlias(static::class, 'carrier_categories');
    }
}
