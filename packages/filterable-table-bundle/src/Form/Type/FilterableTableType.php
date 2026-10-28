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

namespace Vyfony\Bundle\FilterableTableBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\FilterConfiguratorInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting\DbSortConfigurationInterface;

final class FilterableTableType extends AbstractType
{
    /**
     * @var FilterConfiguratorInterface
     */
    private $filterConfigurator;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        FilterConfiguratorInterface $filterConfigurator,
        EntityManager $entityManager
    ) {
        $this->filterConfigurator = $filterConfigurator;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $sortConfiguration = $this->filterConfigurator->getSortConfiguration();

        if ($sortConfiguration instanceof DbSortConfigurationInterface) {
            $builder
                ->add('sortBy', HiddenType::class)
                ->add('sortOrder', HiddenType::class)
                ->add('page', HiddenType::class);
        }

        $builder
            ->add('requestId', HiddenType::class, ['attr' => ['data-vyfony-filterable-table-request-id-input' => true]])
        ;

        foreach ($this->filterConfigurator->getFilterParameters() as $filterParameter) {
            $builder->add(
                $filterParameter->getQueryParameterName(),
                $filterParameter->getType(),
                $filterParameter->getOptions($this->entityManager)
            );
        }

        foreach ($this->filterConfigurator->getTableParameters() as $tableParameter) {
            $builder->add(
                $tableParameter->getQueryParameterName(),
                $tableParameter->getType(),
                $tableParameter->getOptions($this->entityManager)
            );
        }

        if ($sortConfiguration instanceof DbSortConfigurationInterface) {
            $builder->add(
                'disablePagination',
                CheckboxType::class,
                [
                    'label' => $sortConfiguration->getDisablePaginationLabel(),
                    'required' => false,
                ]
            );
        }

        $builder
            ->add('submit', SubmitType::class, $this->filterConfigurator->createSubmitButtonOptions())
            ->add('reset', ButtonType::class, $this->getResetButtonOptions())
            ->add('searchInFound', SubmitType::class, $this->getSearchInFoundButtonOptions())
        ;
    }

    /**
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults($this->filterConfigurator->createDefaults());
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    private function getResetButtonOptions(): array
    {
        $resetButtonOptions = $this->filterConfigurator->createResetButtonOptions();

        if (!\array_key_exists('attr', $resetButtonOptions)) {
            $resetButtonOptions['attr'] = [];
        }

        $resetButtonOptions['attr'] = array_merge(
            $resetButtonOptions['attr'],
            ['data-vyfony-filterable-table-reset-button' => true]
        );

        return $resetButtonOptions;
    }

    private function getSearchInFoundButtonOptions(): array
    {
        $searchInFoundButtonOptions = $this->filterConfigurator->createSearchInFoundButtonOptions();

        if (!\array_key_exists('attr', $searchInFoundButtonOptions)) {
            $searchInFoundButtonOptions['attr'] = [];
        }

        $searchInFoundButtonOptions['attr'] = array_merge(
            $searchInFoundButtonOptions['attr'],
            [
                'style' => 'display: none',
                'data-vyfony-filterable-table-search-in-found-button' => true,
            ]
        );

        return $searchInFoundButtonOptions;
    }
}
