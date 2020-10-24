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

namespace App\Services\GoogleDrive\FileUrlGetter;

use App\Helper\ArrayHelper;
use App\Helper\StringHelper;
use App\Helper\UrlHelper;
use LogicException;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class FileUrlGetter implements FileUrlGetterInterface
{
    private const API_ENDPOINT = 'https://www.googleapis.com/drive/v3';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $googleDriveRootFolder;

    /**
     * @var string
     */
    private $googleDriveApiKey;

    public function __construct(
        LoggerInterface $logger,
        HttpClientInterface $httpClient,
        CacheInterface $cache,
        string $googleDriveRootFolder,
        string $googleDriveApiKey
    ) {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->cache = $cache;
        $this->googleDriveRootFolder = $googleDriveRootFolder;
        $this->googleDriveApiKey = $googleDriveApiKey;
    }

    public function getFileUrl(string $fileName): ?string
    {
        try {
            $createKey = static function (string $fileName): string {
                return 'epigraphy_image_'.$fileName;
            };

            $key = $createKey($fileName);

            $cachedValue = $this->cache->get($key);

            if (null !== $cachedValue) {
                return $cachedValue;
            }

            $fullFileList = $this->getFileListRecursively($this->googleDriveRootFolder);

            $this->cache->setMultiple(
                ArrayHelper::mapKeys(
                    $fullFileList,
                    $createKey
                )
            );

            if (\array_key_exists($fileName, $fullFileList)) {
                return $fullFileList[$fileName];
            }
        } catch (Throwable $exception) {
            $this->logger->error(
                sprintf('Error getting file url for file "%s": %s.', $fileName, $exception->getMessage()),
                [
                    'exception' => $exception,
                ]
            );
        }

        return null;
    }

    private function getFileListRecursively(string $folderId): array
    {
        $queryParameters = [
            'q' => '\''.$folderId.'\' in parents',
            'fields' => 'files(id, name, kind, mimeType, webContentLink)',
            'key' => $this->googleDriveApiKey,
        ];

        $url = self::API_ENDPOINT.'/files/'.UrlHelper::formatQueryParameters($queryParameters);

        $response = $this->httpClient->request(
            'GET',
            $url,
            [
                'headers' => [
                    'referer' => 'https://drive.google.com/',
                ],
            ]
        );

        $statusCode = $response->getStatusCode();

        if (Response::HTTP_OK !== $statusCode) {
            throw new RuntimeException(sprintf('Request to "%s" failed with status code "%d".', $url, $statusCode));
        }

        $decodedResponse = (array) json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $folderContent = (array) $decodedResponse['files'];

        $groupedFolderContent = ArrayHelper::group(
            $folderContent,
            static function ($googleDriveItem): string {
                if ('drive#file' === $googleDriveItem['kind']) {
                    return 'application/vnd.google-apps.folder' === $googleDriveItem['mimeType'] ? 'folders' : 'files';
                }

                return 'other';
            }
        );

        $fullFileList = [];

        if (\array_key_exists('folders', $groupedFolderContent)) {
            $nestedFoldersContent = [];

            foreach ($groupedFolderContent['folders'] as $nestedFolder) {
                $nestedFoldersContent[] = $this->getFileListRecursively($nestedFolder['id']);
            }

            $fullFileList = array_merge($fullFileList, ...$nestedFoldersContent);
        }

        if (\array_key_exists('files', $groupedFolderContent)) {
            foreach ($groupedFolderContent['files'] as $file) {
                if (!\array_key_exists('webContentLink', $file) || !\array_key_exists('name', $file)) {
                    $message = sprintf(
                        'File "%s" in folder %s has unexpected structure.',
                        json_encode($file, JSON_THROW_ON_ERROR, 512),
                        $folderId
                    );

                    throw new LogicException($message);
                }

                $fullFileList[$file['name']] = StringHelper::removeFromEnd(
                    $file['webContentLink'],
                    '&export=download'
                );
            }
        }

        return $fullFileList;
    }
}
