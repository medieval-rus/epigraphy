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

final class CarrierCategoryFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
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
        return 'carrier-category';
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
            'label' => 'controller.inscription.list.filter.carrierCategory',
            'attr' => ['data-vyfony-filterable-table-filter-parameter' => true],
            'choice_attr' => function (CarrierCategory $choice) {
                $superCategory = $choice->getSupercategory();

                return [
                    'data-parent-id' => null === $superCategory ? '' : (string) $superCategory->getId(),
                ];
            },
            'class' => CarrierCategory::class,
            'choice_label' => static function (?CarrierCategory $choice) use ($locale): string {
                if (null === $choice) {
                    return '';
                }

                $label = (string) $choice->getTranslatedName($locale);

                return null === $choice->getSupercategory() ? $label : "\u{00A0}\u{00A0}\u{00A0}\u{00A0}{$label}";
            },
            'expanded' => false,
            'multiple' => true,
            'query_builder' => function (EntityRepository $repository): QueryBuilder {
                $entityAlias = $this->createAlias();
                $parentAlias = $this->aliasFactory->createAlias(static::class, 'parent');

                return $repository
                    ->createQueryBuilder($entityAlias)
                    ->leftJoin($entityAlias.'.supercategory', $parentAlias)
                    ->addSelect(
                        'CASE WHEN '.$entityAlias.'.supercategory IS NULL '.
                        'THEN '.$entityAlias.'.name ELSE '.$parentAlias.'.name END AS HIDDEN group_name'
                    )
                    ->addSelect(
                        'CASE WHEN '.$entityAlias.'.supercategory IS NULL THEN 0 ELSE 1 END AS HIDDEN level_sort'
                    )
                    ->orderBy('group_name', 'ASC')
                    ->addOrderBy('level_sort', 'ASC')
                    ->addOrderBy($entityAlias.'.name', 'ASC');
            },
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

        $ids = [];

        foreach ($formData as $entity) {
            if (!$entity instanceof CarrierCategory) {
                continue;
            }

            $ids[$entity->getId()] = true;

            foreach ($entity->getSubcategories() as $subcategory) {
                $ids[$subcategory->getId()] = true;
            }
        }

        $ids = array_keys($ids);

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
