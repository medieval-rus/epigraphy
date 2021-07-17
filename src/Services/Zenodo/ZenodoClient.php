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

namespace App\Services\Zenodo;

use App\Helper\UrlHelper;
use DateTime;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ZenodoClient implements ZenodoClientInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $zenodoClientEndpoint;

    /**
     * @var string
     */
    private $zenodoClientAccessToken;

    public function __construct(
        LoggerInterface $logger,
        HttpClientInterface $httpClient,
        string $zenodoClientEndpoint,
        string $zenodoClientAccessToken
    ) {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->zenodoClientEndpoint = $zenodoClientEndpoint;
        $this->zenodoClientAccessToken = $zenodoClientAccessToken;
    }

    public function getEndpoint(): string
    {
        $this->logger->info('[ZenodoClient] <getEndpoint>');

        return $this->zenodoClientEndpoint;
    }

    public function getLatestDepositionIdVersion(string $recordId): string
    {
        $this->logger->info(sprintf('[ZenodoClient] <getLatestDepositionIdVersion> $recordId = "%s"', $recordId));

        $recordData = $this->getRecord($recordId);

        return $recordData['metadata']['relations']['version'][0]['last_child']['pid_value'];
    }

    public function getRecord(string $recordId): array
    {
        $this->logger->info(sprintf('[ZenodoClient] <getRecord> $recordId = "%s"', $recordId));

        $url = $this->zenodoClientEndpoint.'/api/records/'.$recordId;

        $response = $this->sendRequest('GET', $url);

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_OK !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        return (array) json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
    }

    public function getDepositions(): array
    {
        $this->logger->info('[ZenodoClient] <getDepositions>');

        $url = $this->zenodoClientEndpoint.'/api/deposit/depositions';

        $response = $this->sendRequest('GET', $url);

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_OK !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        return (array) json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
    }

    public function getDeposition(string $depositionId): array
    {
        $this->logger->info(sprintf('[ZenodoClient] <getDeposition> $depositionId = "%s"', $depositionId));

        $url = $this->zenodoClientEndpoint.'/api/deposit/depositions/'.$depositionId;

        $response = $this->sendRequest('GET', $url);

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_OK !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        return (array) json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
    }

    public function createDeposition(
        DateTime $publicationDate,
        string $title,
        string $description,
        array $keywords,
        array $communities,
        array $creators
    ): array {
        $this->logger->info(
            sprintf(
                '[ZenodoClient] <createDeposition> $publicationDate = "%s"; $title = "%s"; $description = "%s"',
                $publicationDate->format('Y-m-d H:i:s'),
                $title,
                $description
            )
        );

        $url = $this->zenodoClientEndpoint.'/api/deposit/depositions';

        $response = $this->sendRequest(
            'POST',
            $url,
            [
                'body' => json_encode(
                    [
                        'metadata' => [
                            'upload_type' => 'other',
                            'access_right' => 'open',
                            'communities' => $communities,
                            'creators' => $creators,
                            'title' => $title,
                            'description' => $description,
                            'publication_date' => $publicationDate->format('Y-m-d'),
                            'keywords' => $keywords,
                        ],
                    ],
                    \JSON_THROW_ON_ERROR,
                    512
                ),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_CREATED !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        return (array) json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
    }

    public function publishDeposition(string $depositionId): array
    {
        $this->logger->info(sprintf('[ZenodoClient] <publishDeposition> $depositionId = "%s"', $depositionId));

        $url = $this->zenodoClientEndpoint.'/api/deposit/depositions/'.$depositionId.'/actions/publish';

        $response = $this->sendRequest(
            'POST',
            $url,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_ACCEPTED !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        return (array) json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
    }

    public function newVersion(string $depositionId): string
    {
        $this->logger->info(sprintf('[ZenodoClient] <newVersion> $depositionId = "%s"', $depositionId));

        $url = $this->zenodoClientEndpoint.'/api/deposit/depositions/'.$depositionId.'/actions/newversion';

        $response = $this->sendRequest(
            'POST',
            $url,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_CREATED !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        $newVersionResponse = (array) json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

        $parts = explode('/', $newVersionResponse['links']['latest_draft']);

        return $parts[\count($parts) - 1];
    }

    public function deleteVersion(string $depositionId): void
    {
        $this->logger->info(sprintf('[ZenodoClient] <deleteVersion> $depositionId = "%s"', $depositionId));

        $url = $this->zenodoClientEndpoint.'/api/deposit/depositions/'.$depositionId;

        $response = $this->sendRequest(
            'DELETE',
            $url,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_NO_CONTENT !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }
    }

    public function getFiles(string $depositionId): array
    {
        $this->logger->info(sprintf('[ZenodoClient] <getFiles> $depositionId = "%s"', $depositionId));

        $url = $this->zenodoClientEndpoint.'/api/deposit/depositions/'.$depositionId.'/files';

        $response = $this->sendRequest('GET', $url);

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_OK !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        return (array) json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
    }

    public function saveFile(
        string $fileName,
        string $file,
        string $depositionId
    ): array {
        $this->logger->info(
            sprintf(
                '[ZenodoClient] <saveFile> $fileName = "%s" $depositionId = "%s"',
                $fileName,
                $depositionId
            )
        );

        $url = $this->zenodoClientEndpoint.'/api/deposit/depositions/'.$depositionId.'/files';

        $formData = new FormDataPart(
            [
                'name' => $fileName,
                'file' => new DataPart($file, $fileName),
            ]
        );

        $response = $this->sendRequest(
            'POST',
            $url,
            [
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToString(),
            ]
        );

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_CREATED !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        $saveFileResponse = (array) json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

        if ($saveFileResponse['filename'] !== $fileName) {
            $message = sprintf('Illegal file name "%s", should be "%s"', $fileName, $saveFileResponse['filename']);
            throw new RuntimeException($message);
        }

        return $saveFileResponse;
    }

    public function removeFile(string $fileId, string $depositionId): void
    {
        $this->logger->info(sprintf('[ZenodoClient] <removeFile> $depositionId = "%s"', $depositionId));

        $url = $this->zenodoClientEndpoint.'/api/deposit/depositions/'.$depositionId.'/files/'.$fileId;

        $response = $this->sendRequest('DELETE', $url);

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_NO_CONTENT !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }
    }

    public function createAndPublishDeposition(
        DateTime $publicationDate,
        string $title,
        string $description,
        array $keywords,
        array $communities,
        array $creators,
        string $readmeContent
    ): string {
        $this->logger->info(
            sprintf(
                '[ZenodoClient] <createAndPublishDeposition> '.
                '$publicationDate = "%s"; '.
                '$title = "%s"; '.
                '$description = "%s"',
                $publicationDate->format('Y-m-d H:i:s'),
                $title,
                $description
            )
        );

        $deposition = $this->createDeposition(
            $publicationDate,
            $title,
            $description,
            $keywords,
            $communities,
            $creators
        );

        $depositionId = (string) $deposition['id'];

        $this->saveFile('README.txt', $readmeContent, $depositionId);

        $this->publishDeposition($depositionId);

        return $depositionId;
    }

    private function sendRequest(string $method, string $url, array $options = [])
    {
        $this->logger->debug(
            sprintf(
                '[ZenodoClient] <sendRequest> '.
                '$method = "%s"; '.
                '$url = "%s"; '.
                '$options = "%s"; '.
                'get_class($this->httpClient) = "%s"',
                $method,
                $url,
                json_encode($options),
                \get_class($this->httpClient)
            )
        );

        $options['timeout'] = 3600;

        return $this->httpClient->request(
            $method,
            $url.$this->formatQueryParameters(),
            $options
        );
    }

    private function formatQueryParameters(array $queryParameters = [])
    {
        return UrlHelper::formatQueryParameters(
            array_merge(
                [
                    'access_token' => $this->zenodoClientAccessToken,
                ],
                $queryParameters
            )
        );
    }
}
