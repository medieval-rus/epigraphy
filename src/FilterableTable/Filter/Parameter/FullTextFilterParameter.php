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
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Alias\AliasFactoryInterface;
use TeamTNT\TNTSearch\TNTSearch;

final class FullTextFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    private TNTSearch $search;

    public function __construct(AliasFactoryInterface $aliasFactory)
    {
        $this->aliasFactory = $aliasFactory;
        $db_url = $_ENV['DATABASE_URL'];
        $db_parameters = parse_url($db_url);

        $this->search = new TNTSearch;
        $config = [
            'driver' => $db_parameters["scheme"],
            'host' => $db_parameters["host"],
            'database' => str_replace('/', '', $db_parameters["path"]),
            'username' => $db_parameters["user"],
            'password' => $db_parameters["pass"],
            'storage' => './',
            'stemmer' => \TeamTNT\TNTSearch\Stemmer\RussianStemmer::class,
            'charset' => 'utf8'
        ];
        $this->search->loadConfig($config);
        $this->search->fuzziness = true;
        $this->search->selectIndex('fulltext.search');
    }

    public function getQueryParameterName(): string
    {
        return 'fulltext';
    }

    public function getType(): string
    {
        return CKEditorType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        return [
            'label' => 'controller.inscription.list.filter.fulltext',
            'autoload' => true,
            'config_name' => 'searchconfig',
            'attr' => [
                'data-vyfony-filterable-table-filter-parameter' => true,
                'data-important' => true,
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

        $results = $this->search->search($filterValue, 500);
        $ids = $results["ids"];

        return (string) $queryBuilder->expr()->in($entityAlias.'.id', $ids);
    }
}
