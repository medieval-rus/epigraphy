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

namespace App\Admin\Abstraction;

use App\Persistence\Entity\Epigraphy\NamedEntityInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
abstract class AbstractNamedEntityAdmin extends AbstractEntityAdmin
{
    /**
     * @param NamedEntityInterface $object
     */
    public function toString($object): string
    {
        return $object->getName();
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createLabeledListOptions('id'))
            ->add('name', null, $this->createLabeledListOptions('name'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('form.'.$this->getEntityKey().'.section.name.label')
                ->add(
                    'name',
                    TextType::class,
                    $this->createLabeledFormOptions('name', ['required' => true])
                )
            ->end()
        ;
    }
}
