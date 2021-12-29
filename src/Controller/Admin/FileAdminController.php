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

namespace App\Controller\Admin;

use App\Persistence\Entity\Media\File;
use App\Services\Media\Thumbnails\ThumbnailsGeneratorInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class FileAdminController extends CRUDController
{
    private ThumbnailsGeneratorInterface $thumbnailsGenerator;

    public function __construct(ThumbnailsGeneratorInterface $thumbnailsGenerator)
    {
        $this->thumbnailsGenerator = $thumbnailsGenerator;
    }

    public function regenerateThumbnailsAction(): Response
    {
        /**
         * @var $file File
         */
        $file = $this->admin->getSubject();

        $this->thumbnailsGenerator->regenerateAll($file);

        $this->addFlash('sonata_flash_success', $this->trans('action.regenerateThumbnails.flash'));

        return new RedirectResponse($this->admin->generateObjectUrl('edit', $file));
    }
}
