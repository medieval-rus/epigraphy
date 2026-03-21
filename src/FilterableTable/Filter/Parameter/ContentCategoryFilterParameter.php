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

use App\Persistence\Entity\Epigraphy\ContentCategory;
use App\Services\Epigraphy\Localization\LocalizedTextService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\RequestStack;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Alias\AliasFactoryInterface;

final class ContentCategoryFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    private AliasFactoryInterface $aliasFactory;
    private LocalizedTextService $localizedTextService;
    private RequestStack $requestStack;

    public function __construct(
        AliasFactoryInterface $aliasFactory,
        LocalizedTextService $localizedTextService,
        RequestStack $requestStack
    )
    {
        $this->aliasFactory = $aliasFactory;
        $this->localizedTextService = $localizedTextService;
        $this->requestStack = $requestStack;
    }

    public function getQueryParameterName(): string
    {
        return 'content-category';
    }

    public function getType(): string
    {
        return EntityType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        return [
            'label' => 'controller.inscription.list.filter.contentCategory',
            'attr' => ['data-vyfony-filterable-table-filter-parameter' => true],
            'choice_attr' => function (ContentCategory $choice) {
                $superCategory = $choice->getSupercategory();

                return [
                    'data-parent-id' => null === $superCategory ? '' : (string) $superCategory->getId(),
                ];
            },
            'class' => ContentCategory::class,
            'choice_label' => function (?ContentCategory $choice): string {
                if (null === $choice) {
                    return '';
                }

                $request = $this->requestStack->getCurrentRequest();
                $locale = null === $request ? null : $request->getLocale();
                $label = (string) $this->localizedTextService->resolveForEntity($choice, 'name', $choice->getName(), $locale);

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
            if (!$entity instanceof ContentCategory) {
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
                $entityAlias.'.zeroRow',
                $zeroRowAlias = $this->aliasFactory->createAlias(static::class, 'zero_row')
            )
            ->leftJoin(
                $zeroRowAlias.'.contentCategoriesReferences',
                $interpretationsAlias = $this->aliasFactory->createAlias(static::class, 'references')
            )
            ->leftJoin(
                $zeroRowAlias.'.contentCategories',
                $contentCategoryOfZeroRowAlias = $this->createAlias()
            )
            ->leftJoin(
                $interpretationsAlias.'.contentCategories',
                $contentCategoryOfInterpretationAlias = $this->createAlias()
            )
        ;

        return (string) $queryBuilder->expr()->orX(
            $queryBuilder->expr()->in($contentCategoryOfZeroRowAlias.'.id', $ids),
            $queryBuilder->expr()->in($contentCategoryOfInterpretationAlias.'.id', $ids)
        );
    }

    private function createAlias(): string
    {
        return $this->aliasFactory->createAlias(static::class, 'content_categories');
    }
}
