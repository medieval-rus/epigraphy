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

namespace Vyfony\Bundle\FilterableTableBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

final class VyfonyFilterableTableExtension extends ConfigurableExtension
{
    /**
     * @throws Exception
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yaml');

        $container->setParameter(
            'vyfony_filterable_table.table_configurator',
            $mergedConfig['table_configurator']
        );

        $container->setParameter(
            'vyfony_filterable_table.filter_configurator',
            $mergedConfig['filter_configurator']
        );

        $container->setParameter(
            'vyfony_filterable_table.entity_class',
            $mergedConfig['entity_class']
        );
    }
}
