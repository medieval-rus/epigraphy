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

namespace Vyfony\Bundle\FilterableTableBundle\Form\Data;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\FilterConfiguratorInterface;
use Vyfony\Bundle\FilterableTableBundle\Form\Transformer\QueryParametersTransformerInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Configurator\TableConfiguratorInterface;

final class FormData implements FormDataInterface
{
    /**
     * @var array
     */
    private $requestData;

    /**
     * @var QueryParametersTransformerInterface
     */
    private $queryParametersTransformer;

    public function __construct(
        RequestStack $requestStack,
        QueryParametersTransformerInterface $queryParametersTransformer
    ) {
        $this->requestData = $requestStack->getCurrentRequest()->query->all();
        $this->queryParametersTransformer = $queryParametersTransformer;
    }

    public function getForSubmission(
        TableConfiguratorInterface $tableConfigurator,
        FilterConfiguratorInterface $filterConfigurator
    ): array {
        return $this->queryParametersTransformer->transformQueryParametersForFormSubmission(
            $this->getQueryParameters($tableConfigurator, $filterConfigurator),
            $this->getDefaultQueryParameters($tableConfigurator, $filterConfigurator)
        );
    }

    public function getForDataCollection(FormInterface $form): array
    {
        return $this->queryParametersTransformer->transformFormDataForDataCollection(
            $form->getData(),
            $this->requestData
        );
    }

    public function getQueryParameters(
        TableConfiguratorInterface $tableConfigurator,
        FilterConfiguratorInterface $filterConfigurator
    ): array {
        return array_merge(
            $this->getDefaultQueryParameters($tableConfigurator, $filterConfigurator),
            $this->requestData
        );
    }

    public function getDefaultQueryParameters(
        TableConfiguratorInterface $tableConfigurator,
        FilterConfiguratorInterface $filterConfigurator
    ): array {
        return array_merge(
            $tableConfigurator->getDefaultTableParameters(),
            $filterConfigurator->getDefaultTableParameters()
        );
    }
}
