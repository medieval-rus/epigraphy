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
 * in the hope  that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\DataStorage;

use App\DataStorage\Connectors\Osf\OsfConnectorInterface;
use App\Helper\StringHelper;
use App\Persistence\Entity\Epigraphy\File;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;

final class DataStorageManager implements DataStorageManagerInterface
{
    private OsfConnectorInterface $osfConnector;

    private string $osfPhotosFolderId;

    private string $osfDrawingsFolderId;

    private string $osfTextImagesFolderId;

    public function __construct(
        OsfConnectorInterface $osfConnector,
        string $osfPhotosFolderId,
        string $osfDrawingsFolderId,
        string $osfTextImagesFolderId
    ) {
        $this->osfConnector = $osfConnector;
        $this->osfPhotosFolderId = $osfPhotosFolderId;
        $this->osfDrawingsFolderId = $osfDrawingsFolderId;
        $this->osfTextImagesFolderId = $osfTextImagesFolderId;
    }

    public function prePersist(File $file, UploadedFile $uploadedFile): void
    {
        $fileName = $uploadedFile->getClientOriginalName();

        switch (true) {
            case StringHelper::startsWith($fileName, 'photo_'):
                $remoteFolderId = $this->osfPhotosFolderId;
                break;
            case StringHelper::startsWith($fileName, 'drawing_'):
                $remoteFolderId = $this->osfDrawingsFolderId;
                break;
            case StringHelper::startsWith($fileName, 'text_'):
                $remoteFolderId = $this->osfTextImagesFolderId;
                break;
            default:
                throw new RuntimeException(sprintf('Unexpected file name %s', $fileName));
        }

        $uploadUrl = $this->osfConnector->getUploadUrl($remoteFolderId);

        try {
            [$id, $hash, $url] = $this->osfConnector->uploadFile($uploadUrl, $fileName, $uploadedFile->getRealPath());
        } catch (Throwable $exception) {
            // todo roll back the changes (try to remove uploaded file)
            throw $exception;
        }

        $file->setFileName($fileName);
        $file->setMediaType($uploadedFile->getMimeType());
        $file->setUrl($url);
        $file->setHash($hash);
        $file->setOsfFileId((string) $id);
    }
}
