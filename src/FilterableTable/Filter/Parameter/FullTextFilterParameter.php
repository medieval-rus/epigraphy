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
        $this->search->selectIndex('/thumbs/fulltext.sql');
    }

    public function getQueryParameterName(): string
    {
        return 'fulltext';
    }

    public function getType(): string
    {
        return TextType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        return [
            'label' => 'controller.inscription.list.filter.fulltext',
            'attr' => [
                'data-vyfony-filterable-table-filter-parameter' => true,
                'data-important' => true,
                'data-virtual-keyboard' => true,
            ],
        ];
    }

    /**
     * @param mixed $formData
     */
    public function buildWhereExpression(QueryBuilder $queryBuilder, $formData, string $entityAlias): ?string
    {
        $filterValue = $formData;

        if (null === $filterValue || trim($filterValue) === '') {
            return null;
        }

        // Разбиваем запрос на слова (токены)
        $tokens = $this->search->breakIntoTokens($filterValue);
        
        if (count($tokens) === 0) {
            return null;
        }

        // Для каждого слова находим документы, где оно встречается
        $docIdsSets = [];
        foreach ($tokens as $token) {
            // Пропускаем пустые токены
            $token = trim($token);
            if ($token === '') {
                continue;
            }

            // Стеммируем слово (приводим к базовой форме)
            $stemmedToken = $this->search->getStemmer()->stem($token);
            
            // Получаем все документы, содержащие это слово
            $documents = $this->search->getAllDocumentsForKeyword($stemmedToken, true);
            
            // Извлекаем ID документов
            $docIds = [];
            foreach ($documents as $document) {
                $docIds[] = (int) $document['doc_id'];
            }
            
            // Если хотя бы для одного слова нет документов, возвращаем пустой результат
            if (count($docIds) === 0) {
                return '1 = 0';
            }
            
            $docIdsSets[] = $docIds;
        }

        // Если не было валидных токенов
        if (count($docIdsSets) === 0) {
            return null;
        }

        // Находим пересечение всех множеств (AND логика)
        // Документ должен содержать ВСЕ слова из запроса
        $intersection = $docIdsSets[0];
        for ($i = 1; $i < count($docIdsSets); $i++) {
            $intersection = array_intersect($intersection, $docIdsSets[$i]);
        }

        // Убираем дубликаты и переиндексируем массив
        $ids = array_values(array_unique($intersection));

        if (count($ids) === 0) {
            return '1 = 0';
        }

        // Ограничиваем количество результатов
        if (count($ids) > 500) {
            $ids = array_slice($ids, 0, 500);
        }

        return (string) $queryBuilder->expr()->in($entityAlias.'.id', $ids);
    }
}
