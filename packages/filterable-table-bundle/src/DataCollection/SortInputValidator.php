<?php

declare(strict_types=1);

namespace Vyfony\Bundle\FilterableTableBundle\DataCollection;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting\DbSortConfigurationInterface;

final class SortInputValidator
{
    public static function normalize(array $data, DbSortConfigurationInterface $configuration): array
    {
        $field = $data['sortBy'] ?? $configuration->getDefaultSortBy();
        $direction = $data['sortOrder'] ?? ($configuration->getIsDefaultSortAsc() ? 'asc' : 'desc');
        if (!is_string($field) || !in_array($field, $configuration->getAllowedSortFields(), true)) {
            throw new BadRequestHttpException('Invalid sort field.');
        }
        if (!is_string($direction) || !in_array(strtolower($direction), ['asc', 'desc'], true)) {
            throw new BadRequestHttpException('Invalid sort direction.');
        }
        $page = filter_var($data['page'] ?? 1, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => intdiv(PHP_INT_MAX, max(1, $configuration->getPageSize()))],
        ]);
        if (false === $page) {
            throw new BadRequestHttpException('Invalid page number.');
        }
        $data['sortBy'] = $field;
        $data['sortOrder'] = strtolower($direction);
        $data['page'] = $page;

        return $data;
    }
}
