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

use App\FilterableTable\Filter\Parameter\CityFilterParameter;
use App\FilterableTable\Filter\Parameter\DiscoverySiteFilterParameter;
use App\FilterableTable\Filter\Parameter\AlphabetFilterParameter;
use App\FilterableTable\Filter\Parameter\AuthorFilterParameter;
use App\FilterableTable\Filter\Parameter\CarrierCategoryFilterParameter;
use App\FilterableTable\Filter\Parameter\SuperCarrierCategoryFilterParameter;
use App\FilterableTable\Filter\Parameter\CarrierTypeFilterParameter;
use App\FilterableTable\Filter\Parameter\ContentCategoryFilterParameter;
use App\FilterableTable\Filter\Parameter\SuperContentCategoryFilterParameter;
use App\FilterableTable\Filter\Parameter\MaterialFilterParameter;
use App\FilterableTable\Filter\Parameter\SuperMaterialFilterParameter;
use App\FilterableTable\Filter\Parameter\NumberInSourceFilterParameter;
use App\FilterableTable\Filter\Parameter\Origin1FilterParameter;
use App\FilterableTable\Filter\Parameter\Origin2FilterParameter;
use App\FilterableTable\Filter\Parameter\PreservationStateFilterParameter;
use App\FilterableTable\Filter\Parameter\TextFilterParameter;
use App\FilterableTable\Filter\Parameter\TranslationFilterParameter;
use App\FilterableTable\Filter\Parameter\WritingMethodFilterParameter;
use App\FilterableTable\Filter\Parameter\SuperWritingMethodFilterParameter;
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
    private SuperCarrierCategoryFilterParameter $superCarrierCategoryFilterParameter;
    private WritingMethodFilterParameter $writingMethodFilterParameter;
    private SuperWritingMethodFilterParameter $superWritingMethodFilterParameter;
    private PreservationStateFilterParameter $preservationStateFilterParameter;
    private AlphabetFilterParameter $alphabetFilterParameter;
    private TextFilterParameter $textFilterParameter;
    private MaterialFilterParameter $materialFilterParameter;
    private SuperMaterialFilterParameter $superMaterialFilterParameter;
    private ContentCategoryFilterParameter $contentCategoryFilterParameter;
    private SuperContentCategoryFilterParameter $superContentCategoryFilterParameter;
    private AuthorFilterParameter $authorFilterParameter;
    private NumberInSourceFilterParameter $numberInSourceFilterParameter;
    private Origin1FilterParameter $origin1FilterParameter;
    private Origin2FilterParameter $origin2FilterParameter;
    private TranslationFilterParameter $translationFilterParameter;
    private CityFilterParameter $cityFilterParameter;
    private DiscoverySiteFilterParameter $discoverySiteFilterParameter;

    public function __construct(
        ValueStringifierInterface $valueStringifier,
        CarrierTypeFilterParameter $carrierTypeFilterParameter,
        CarrierCategoryFilterParameter $carrierCategoryFilterParameter,
        SuperCarrierCategoryFilterParameter $superCarrierCategoryFilterParameter,
        WritingMethodFilterParameter $writingMethodFilterParameter,
        SuperWritingMethodFilterParameter $superWritingMethodFilterParameter,
        PreservationStateFilterParameter $preservationStateFilterParameter,
        AlphabetFilterParameter $alphabetFilterParameter,
        TextFilterParameter $textFilterParameter,
        MaterialFilterParameter $materialFilterParameter,
        SuperMaterialFilterParameter $superMaterialFilterParameter,
        ContentCategoryFilterParameter $contentCategoryFilterParameter,
        SuperContentCategoryFilterParameter $superContentCategoryFilterParameter,
        AuthorFilterParameter $authorFilterParameter,
        NumberInSourceFilterParameter $numberInSourceFilterParameter,
        Origin1FilterParameter $origin1FilterParameter,
        Origin2FilterParameter $origin2FilterParameter,
        TranslationFilterParameter $translationFilterParameter,
        DiscoverySiteFilterParameter $discoverySiteFilterParameter,
        CityFilterParameter $cityFilterParameter
    ) {
        $args = func_get_args();
        $reflection_method = new \ReflectionMethod($this, '__construct');
        $parameters = $reflection_method->getParameters();

        for ($idx = 0; $idx < count($args); $idx++) {
            $this->{$parameters[$idx]->name} = $args[$idx];
        }
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
            // типология носителя
            $this->superCarrierCategoryFilterParameter,
            $this->carrierCategoryFilterParameter,            
            $this->superMaterialFilterParameter,
            $this->materialFilterParameter,
            $this->origin1FilterParameter,
            $this->origin2FilterParameter,
            $this->cityFilterParameter,
            $this->discoverySiteFilterParameter,
            // типология надписи
            $this->superWritingMethodFilterParameter,    
            $this->writingMethodFilterParameter,
            $this->superContentCategoryFilterParameter,
            $this->contentCategoryFilterParameter,
            $this->alphabetFilterParameter,
            $this->preservationStateFilterParameter,
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
        return [];
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
