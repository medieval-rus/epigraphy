<?php

declare(strict_types=1);

/*
 * This file is part of VyfonyFilterableTableBundle project.
 *
 * (c) Anton Dyshkant <vyshkant@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Parameter;

final class ParameterFactory implements ParameterFactoryInterface
{
    public function createParameter(): string
    {
        return $this->createNamedParameter(uniqid());
    }

    public function createNamedParameter(string $parameterName): string
    {
        return ':'.str_replace('.', '_', $parameterName).'_'.uniqid();
    }
}
