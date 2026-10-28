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

namespace Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table;

use Doctrine\ORM\EntityManager;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\AbstractFilterParameter;

abstract class AbstractTableParameter extends AbstractFilterParameter implements TableParameterInterface
{
    protected function createOptions(EntityManager $entityManager): array
    {
        return array_merge(parent::createOptions($entityManager), [
            'label_attr' => ['class' => ''],
            'required' => true,
        ]);
    }
}
