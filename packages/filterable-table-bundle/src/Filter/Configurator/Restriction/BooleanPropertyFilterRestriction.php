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

namespace Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Restriction;

use Doctrine\ORM\Query\Expr;

final class BooleanPropertyFilterRestriction extends AbstractFilterRestriction
{
    /**
     * @var bool
     */
    private $value;

    public function buildWhereExpression(Expr $expressionBuilder, string $entityAlias): string
    {
        return (string) $expressionBuilder->eq($entityAlias.'.'.$this->getName(), $this->value);
    }

    /**
     * @return BooleanPropertyFilterRestriction
     */
    public function setValue(bool $value): self
    {
        $this->value = $value;

        return $this;
    }
}
