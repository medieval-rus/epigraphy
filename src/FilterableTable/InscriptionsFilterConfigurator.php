<?php

declare(strict_types=1);

/*
 * This file is part of «Epigraphy of Medieval Rus'» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus'» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus'» database is distributed
 * in the hope  that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus'» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\FilterableTable;

use App\Persistence\Entity\Inscription;
use InvalidArgumentException;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\AbstractFilterConfigurator;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\TableParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Restriction\FilterRestrictionInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class InscriptionsFilterConfigurator extends AbstractFilterConfigurator
{
    /**
     * @return array
     */
    public function createSubmitButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-primary'],
            'label' => 'controller.inscription.list.filter.submitButton',
        ];
    }

    /**
     * @return array
     */
    public function createResetButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-secondary'],
            'label' => 'controller.inscription.list.filter.resetButton',
        ];
    }

    /**
     * @return array
     */
    public function createSearchInFoundButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-secondary'],
            'label' => 'controller.inscription.list.filter.searchInFoundButton',
        ];
    }

    /**
     * @return array
     */
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
     * @return string
     */
    public function getDisablePaginationLabel(): string
    {
        return 'controller.inscription.list.filter.disablePaginator';
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
        return [];
    }

    /**
     * @return FilterParameterInterface[]
     */
    protected function createFilterParameters(): array
    {
        return [];
    }

    /**
     * @return TableParameterInterface[]
     */
    protected function createTableParameters(): array
    {
        return [];
    }
}
