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

namespace App\Admin\Epigraphy;

use App\Admin\AbstractNamedEntityAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;


final class DiscoverySiteAdmin extends AbstractNamedEntityAdmin
{
    protected $baseRouteName = 'epigraphy_discovery_site';

    protected $baseRoutePattern = 'epigraphy/discovery-site';    

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name', null, $this->createFormOptions('name'))
            ->add(
                'nameAliases',
                CollectionType::class,
                $this->createFormOptions(
                    'nameAliases',
                    [
                        'entry_type' => TextType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'delete_empty' => true,
                        'required' => false
                    ]
                )
            )
            ->add('rivers', null, $this->createManyToManyFormOptions('rivers'))
            ->add('cities', null, $this->createManyToManyFormOptions('cities'))
            ->add('comments', CKEditorType::class, $this->createFormOptions('comments', ['autoload' => false, 'required' => false]))
            ->add('latitude', null, $this->createFormOptions('latitude', ['required' => false]))
            ->add('longitude', null, $this->createFormOptions('longitude', ['required' => false]))
            ->add('isOutsideCity',
                CheckboxType::class,
                $this->createFormOptions('isOutsideCity', ['required' => false])
            )
        ;
    }
}