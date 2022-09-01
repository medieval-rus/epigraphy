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

namespace App\Admin;

use ReflectionClass;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Doctrine\ORM\EntityRepository;

abstract class AbstractEntityAdmin extends AbstractAdmin
{
    protected function configure(): void
    {
        $entityKey = $this->getEntityKey();

        $this->classnameLabel = $entityKey;
        $this->setLabel('menu.paragraphs.'.$entityKey.'.label');
        $this->setTranslationDomain('admin');
    }

    protected function getEntityKey(): string
    {
        return lcfirst((new ReflectionClass($this->getClass()))->getShortName());
    }

    protected function getFormKeyForFieldName(string $fieldName): string
    {
        return 'form.'.$this->getEntityKey().'.fields.'.$fieldName;
    }

    protected function getListKeyForFieldName(string $fieldName): string
    {
        return 'list.'.$this->getEntityKey().'.fields.'.$fieldName;
    }

    protected function createListOptions(string $fieldName, array $options = []): array
    {
        return array_merge(
            [
                'label' => $this->getListKeyForFieldName($fieldName),
            ],
            $options
        );
    }

    protected function createFormOptions(string $fieldName, array $options = []): array
    {
        return array_merge(
            [
                'label' => $this->getFormKeyForFieldName($fieldName),
            ],
            $options
        );
    }

    protected function createManyToManyFormOptions(string $fieldName, array $options = []): array
    {
        return $this->createFormOptions(
            $fieldName,
            array_merge(
                ['required' => false, 'multiple' => true],
                $options
            )
        );
    }

    protected function createFilteredEntityOptions(string $fieldName, string $class, string $param, string $value = 'true'): array
    {
        return $this->createFormOptions(
            $fieldName,
            array_merge([
                'class' => $class,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($param, $value) {
                    return $er->createQueryBuilder('c')->where('c.'.$param.' = '.$value);
                }
            ])
        );
    }

    protected function getSectionLabel(string $key): string
    {
        return 'form.'.$this->getEntityKey().'.section.'.$key.'.label';
    }

    protected function getTabLabel(string $key): string
    {
        return 'form.'.$this->getEntityKey().'.tab.'.$key.'.label';
    }
}
