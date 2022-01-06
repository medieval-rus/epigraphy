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

namespace App\FilterableTable;

use App\FilterableTable\Filter\Parameter\AlphabetFilterParameter;
use App\FilterableTable\Filter\Parameter\AuthorFilterParameter;
use App\FilterableTable\Filter\Parameter\CarrierCategoryFilterParameter;
use App\FilterableTable\Filter\Parameter\CarrierTypeFilterParameter;
use App\FilterableTable\Filter\Parameter\ContentCategoryFilterParameter;
use App\FilterableTable\Filter\Parameter\MaterialFilterParameter;
use App\FilterableTable\Filter\Parameter\NumberInSourceFilterParameter;
use App\FilterableTable\Filter\Parameter\Origin1FilterParameter;
use App\FilterableTable\Filter\Parameter\PreservationStateFilterParameter;
use App\FilterableTable\Filter\Parameter\TextFilterParameter;
use App\FilterableTable\Filter\Parameter\TranslationFilterParameter;
use App\FilterableTable\Filter\Parameter\WritingMethodFilterParameter;
use App\Persistence\Entity\Epigraphy\Inscription;
use App\Services\Epigraphy\ActualValue\Formatter\ActualValueFormatterInterface;
use App\Services\Epigraphy\Stringifier\ValueStringifierInterface;
use InvalidArgumentException;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\AbstractFilterConfigurator;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\RadioColumnChoiceTableParameter;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\RadioOption\RadioOption;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\TableParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Restriction\BooleanPropertyFilterRestriction;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Restriction\FilterRestrictionInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting\CustomSortConfigurationInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting\DbSortConfiguration;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Sorting\DbSortConfigurationInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadata;

final class InscriptionsFilterConfigurator extends AbstractFilterConfigurator
{
    private ValueStringifierInterface $valueStringifier;
    private CarrierTypeFilterParameter $carrierTypeFilterParameter;
    private CarrierCategoryFilterParameter $carrierCategoryFilterParameter;
    private WritingMethodFilterParameter $writingMethodFilterParameter;
    private PreservationStateFilterParameter $preservationStateFilterParameter;
    private AlphabetFilterParameter $alphabetFilterParameter;
    private TextFilterParameter $textFilterParameter;
    private MaterialFilterParameter $materialFilterParameter;
    private ContentCategoryFilterParameter $contentCategoryFilterParameter;
    private AuthorFilterParameter $authorFilterParameter;
    private NumberInSourceFilterParameter $numberInSourceFilterParameter;
    private Origin1FilterParameter $origin1FilterParameter;
    private TranslationFilterParameter $translationFilterParameter;

    public function __construct(
        ValueStringifierInterface $valueStringifier,
        CarrierTypeFilterParameter $carrierTypeFilterParameter,
        CarrierCategoryFilterParameter $carrierCategoryFilterParameter,
        WritingMethodFilterParameter $writingMethodFilterParameter,
        PreservationStateFilterParameter $preservationStateFilterParameter,
        AlphabetFilterParameter $alphabetFilterParameter,
        TextFilterParameter $textFilterParameter,
        MaterialFilterParameter $materialFilterParameter,
        ContentCategoryFilterParameter $contentCategoryFilterParameter,
        AuthorFilterParameter $authorFilterParameter,
        NumberInSourceFilterParameter $numberInSourceFilterParameter,
        Origin1FilterParameter $origin1FilterParameter,
        TranslationFilterParameter $translationFilterParameter
    ) {
        $this->valueStringifier = $valueStringifier;
        $this->carrierTypeFilterParameter = $carrierTypeFilterParameter;
        $this->carrierCategoryFilterParameter = $carrierCategoryFilterParameter;
        $this->writingMethodFilterParameter = $writingMethodFilterParameter;
        $this->preservationStateFilterParameter = $preservationStateFilterParameter;
        $this->alphabetFilterParameter = $alphabetFilterParameter;
        $this->textFilterParameter = $textFilterParameter;
        $this->materialFilterParameter = $materialFilterParameter;
        $this->contentCategoryFilterParameter = $contentCategoryFilterParameter;
        $this->authorFilterParameter = $authorFilterParameter;
        $this->numberInSourceFilterParameter = $numberInSourceFilterParameter;
        $this->origin1FilterParameter = $origin1FilterParameter;
        $this->translationFilterParameter = $translationFilterParameter;
    }

    public function createSubmitButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-primary'],
            'label' => 'controller.inscription.list.filter.submitButton',
        ];
    }

    public function createResetButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-secondary'],
            'label' => 'controller.inscription.list.filter.resetButton',
        ];
    }

    public function createSearchInFoundButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-warning'],
            'label' => 'controller.inscription.list.filter.searchInFoundButton',
        ];
    }

    public function createDefaults(): array
    {
        return [
            'label_attr' => ['class' => ''],
            'translation_domain' => 'messages',
            'attr' => ['class' => 'row'],
            'method' => 'GET',
            'csrf_protection' => false,
            'required' => false,
        ];
    }

    /**
     * @param mixed $entity
     *
     * @return mixed
     */
    public function getEntityId($entity)
    {
        if (!$entity instanceof Inscription) {
            throw new InvalidArgumentException(
                sprintf('Expected entity of type "%s", "%s" given', Inscription::class, $entity)
            );
        }

        return $entity->getId();
    }

    /**
     * @return FilterRestrictionInterface[]
     */
    protected function createFilterRestrictions(): array
    {
        return [
            (new BooleanPropertyFilterRestriction())
                ->setName('isShownOnSite')
                ->setValue(true),
        ];
    }

    /**
     * @return FilterParameterInterface[]
     */
    protected function createFilterParameters(): array
    {
        return [
            $this->carrierTypeFilterParameter,
            $this->carrierCategoryFilterParameter,
            $this->writingMethodFilterParameter,
            $this->preservationStateFilterParameter,
            $this->alphabetFilterParameter,
            $this->materialFilterParameter,
            $this->contentCategoryFilterParameter,
            $this->origin1FilterParameter,
            $this->authorFilterParameter,
            $this->numberInSourceFilterParameter,
            $this->textFilterParameter,
            $this->translationFilterParameter,
        ];
    }

    /**
     * @return TableParameterInterface[]
     */
    protected function createTableParameters(): array
    {
        return [
            (new RadioColumnChoiceTableParameter())
                ->addRadioOption(
                    (new RadioOption())
                        ->setName('description')
                        ->setLabel('controller.inscription.list.filter.dataColumn.option.description')
                        ->setColumnMetadata(
                            (new ColumnMetadata())
                                ->setIsRaw(true)
                                ->setName('interpretation-description')
                                ->setValueExtractor(function (Inscription $inscription): string {
                                    return $this->valueStringifier->stringify($inscription, 'description') ?? '-';
                                })
                                ->setLabel('controller.inscription.list.table.column.interpretation.description')
                        )
                )
                ->addRadioOption(
                    (new RadioOption())
                        ->setName('text')
                        ->setLabel('controller.inscription.list.filter.dataColumn.option.text')
                        ->setColumnMetadata(
                            (new ColumnMetadata())
                                ->setIsRaw(true)
                                ->setName('interpretation-text')
                                ->setValueExtractor(function (Inscription $inscription): string {
                                    return $this->valueStringifier->stringify(
                                        $inscription,
                                        'text',
                                        ActualValueFormatterInterface::FORMAT_TYPE_ORIGINAL_TEXT
                                    ) ?? '-';
                                })
                                ->setLabel('controller.inscription.list.table.column.interpretation.text')
                        )
                )
                ->setQueryParameterName('dataColumn')
                ->setLabel('controller.inscription.list.filter.dataColumn.label'),
        ];
    }

    protected function createDbSortConfiguration(): ?DbSortConfigurationInterface
    {
        return new DbSortConfiguration(
            'id',
            true,
            200,
            3,
            'controller.inscription.list.filter.disablePaginator'
        );
    }

    protected function createCustomSortConfiguration(): ?CustomSortConfigurationInterface
    {
        return null;
    }
}
