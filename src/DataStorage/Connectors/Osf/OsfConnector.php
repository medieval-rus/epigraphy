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

namespace App\DataStorage\Connectors\Osf;

use App\Helper\UrlHelper;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class OsfConnector implements OsfConnectorInterface
{
    private Stopwatch $stopwatch;

    private LoggerInterface $logger;

    private HttpClientInterface $httpClient;

    private string $osfEndpoint;

    private string $osfAccessToken;

    public function __construct(
        Stopwatch $stopwatch,
        LoggerInterface $logger,
        HttpClientInterface $httpClient,
        string $osfEndpoint,
        string $osfAccessToken
    ) {
        $this->stopwatch = $stopwatch;
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->osfEndpoint = $osfEndpoint;
        $this->osfAccessToken = $osfAccessToken;
    }

    public function getUploadUrl(string $folderId): string
    {
        $this->logger->info(sprintf('[OsfConnector] <getUploadUrl> $folderId = "%s"', $folderId));

        $folderData = $this->getFolderData($folderId);

        return $folderData['links']['upload'];
    }

    public function uploadFile(string $folderUploadUrl, string $fileName, string $pathToFile): array
    {
        $this->logger->info(
            sprintf(
                '[OsfConnector] <uploadFile> '.
                '$folderUploadUrl = "%s" '.
                '$fileName = "%s" '.
                '$pathToFile = "%s" ',
                $folderUploadUrl,
                $fileName,
                $pathToFile
            )
        );

        $response = $this->sendRequest(
            'PUT',
            $folderUploadUrl,
            Response::HTTP_CREATED,
            [
                'name' => $fileName,
            ],
            [
                'body' => fopen($pathToFile, 'r'),
            ]
        );

        $remoteFileName = $response['data']['attributes']['name'];

        if ($remoteFileName !== $fileName) {
            throw new RuntimeException(
                sprintf('Remote file name is "%s", while local one is "%s"', $remoteFileName, $fileName)
            );
        }

        return [
            $response['data']['id'],
            $response['data']['attributes']['extra']['hashes']['md5'],
            $response['data']['links']['download'],
        ];
    }

    private function getFolderData(string $folderId): array
    {
        $this->logger->info(sprintf('[OsfConnector] <getFolderData> $folderId = "%s"', $folderId));

        $url = $this->osfEndpoint.'/v2/files/'.$folderId.'/';

        $response = $this->sendRequest('GET', $url, Response::HTTP_OK);

        return $response['data'];
    }

    private function sendRequest(
        string $method,
        string $url,
        int $expectedStatusCode,
        array $queryParameters = [],
        array $options = []
    ): array {
        $this->logger->info(
            sprintf(
                '[OsfConnector] <sendRequest> '.
                '$method = "%s" '.
                '$url = "%s" '.
                '$queryParameters = "%s" '.
                'array_keys($options) = "%s"',
                $method,
                $url,
                json_encode($queryParameters),
                json_encode(array_keys($options))
            )
        );

        $fullUrl = $url.UrlHelper::formatQueryParameters($queryParameters);

        $stopwatchKey = 'osf_request_'.$fullUrl;

        $this->stopwatch->start($stopwatchKey);

        $response = $this->httpClient->request(
            $method,
            $fullUrl,
            array_merge(['auth_bearer' => $this->osfAccessToken], $options)
        );

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        $stopwatchResult = $this->stopwatch->stop($stopwatchKey);

        $this->logger->info(
            sprintf(
                '[OsfConnector] <sendRequest> Request to "%s" completed in %d ms',
                $fullUrl,
                $stopwatchResult->getDuration()
            )
        );

        if ($expectedStatusCode !== $statusCode) {
            throw new RuntimeException(
                sprintf('%s Request to "%s" failed with status code "%d": %s', $method, $fullUrl, $statusCode, $content)
            );
        }

        return (array) json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
    }
}
