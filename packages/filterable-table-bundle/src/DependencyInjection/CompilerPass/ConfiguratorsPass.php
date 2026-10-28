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

namespace Vyfony\Bundle\FilterableTableBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\RouterInterface;

final class ConfiguratorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $tableConfiguratorServiceId = $container->getParameter('vyfony_filterable_table.table_configurator');
        $filterConfiguratorServiceId = $container->getParameter('vyfony_filterable_table.filter_configurator');
        $entityClass = $container->getParameter('vyfony_filterable_table.entity_class');

        $tableConfiguratorServiceDefinition = $container->findDefinition($tableConfiguratorServiceId);
        $filterConfiguratorServiceDefinition = $container->findDefinition($filterConfiguratorServiceId);

        $container
            ->getDefinition('vyfony_filterable_table.table')
            ->setArgument('$tableConfigurator', $tableConfiguratorServiceDefinition)
            ->setArgument('$filterConfigurator', $filterConfiguratorServiceDefinition)
            ->setArgument('$entityClass', $entityClass)
        ;

        $container
            ->getDefinition('vyfony_filterable_table.data_collection.data_collector')
            ->setArgument('$filterConfigurator', $filterConfiguratorServiceDefinition)
        ;

        $container
            ->getDefinition('vyfony_filterable_table.form.type.filterable_table_type')
            ->setArgument('$filterConfigurator', $filterConfiguratorServiceDefinition)
            ->setArgument('$entityManager', $container->findDefinition('doctrine.orm.entity_manager'))
        ;

        $tableConfiguratorServiceDefinition
            ->setArgument('$router', $container->findDefinition(RouterInterface::class))
            ->setArgument('$filterConfigurator', $filterConfiguratorServiceDefinition)
        ;
    }
}
