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

namespace App\Admin\Bibliography;

use App\Admin\AbstractEntityAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

final class ReferencesListItemAdmin extends AbstractEntityAdmin
{
    protected string $baseRouteName = 'bibliography_references_list_item';

    protected string $baseRoutePattern = 'bibliography/references-list/item';

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add(
                'bibliographicRecord.shortName',
                null,
                $this->createLabeledFormOptions('bibliographicRecord', ['disabled' => true])
            )
            ->add('position', HiddenType::class, $this->createLabeledFormOptions('position'))
        ;
    }
}
