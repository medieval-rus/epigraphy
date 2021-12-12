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

namespace App\DataStorage;

use App\DataStorage\Connectors\Osf\OsfConnectorInterface;
use App\Persistence\Entity\Media\File;
use Doctrine\ORM\EntityRepository;
use RuntimeException;
use Throwable;

final class DataStorageManager implements DataStorageManagerInterface
{
    private OsfConnectorInterface $osfConnector;

    private array $osfFolders;

    public function __construct(
        OsfConnectorInterface $osfConnector,
        array $osfFolders
    ) {
        $this->osfConnector = $osfConnector;
        $this->osfFolders = $osfFolders;
    }

    public function upload(File $file, string $fileName, string $pathToSource, string $mimeType): void
    {
        krsort($this->osfFolders);

        $remoteFolderId = null;
        foreach ($this->osfFolders as $folderData) {
            if ($this->fileNameMatchesFolder($fileName, $folderData)) {
                $remoteFolderId = $folderData['id'];
                break;
            }
        }

        if (null === $remoteFolderId) {
            throw new RuntimeException(
                sprintf(
                    'Unexpected file name "%s". Known patterns are: %s',
                    $fileName,
                    implode(
                        ', ',
                        array_map(
                            fn ($folderData): string => sprintf('"%s"', $folderData['pattern']),
                            $this->osfFolders
                        )
                    )
                )
            );
        }

        $uploadUrl = $this->osfConnector->getUploadUrl($remoteFolderId);

        try {
            [$id, $hash, $url] = $this->osfConnector->uploadFile($uploadUrl, $fileName, $pathToSource);
        } catch (Throwable $exception) {
            // todo roll back the changes (try to remove uploaded file)
            throw $exception;
        }

        $file->setFileName($fileName);
        $file->setMediaType($mimeType);
        $file->setUrl($url);
        $file->setHash($hash);
        $file->setOsfFileId((string) $id);
    }

    public function isFileNameValid(string $fileName): bool
    {
        foreach ($this->osfFolders as $folderData) {
            if ($this->fileNameMatchesFolder($fileName, $folderData)) {
                return true;
            }
        }

        return false;
    }

    public function getFolderFilter(string $folderKey): callable
    {
        if (!\array_key_exists($folderKey, $this->osfFolders)) {
            throw new RuntimeException(
                sprintf(
                    'Unknown folder key "%s". Known folder keys are: %s',
                    $folderKey,
                    implode(', ', array_map(fn ($key) => sprintf('"%s"', $key), array_keys($this->osfFolders)))
                )
            );
        }

        return function (?File $file) use ($folderKey): bool {
            return null === $file || $this->fileNameMatchesFolder($file->getFileName(), $this->osfFolders[$folderKey]);
        };
    }

    public function getQueryBuilder(): callable
    {
        return function (EntityRepository $entityRepository) {
            return $entityRepository
                ->createQueryBuilder('f')
                ->orderBy('f.id', 'DESC');
        };
    }

    private function fileNameMatchesFolder(string $fileName, array $folderData): bool
    {
        return 1 === preg_match($folderData['pattern'], $fileName);
    }
}
