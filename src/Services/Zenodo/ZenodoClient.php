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

namespace App\Services\Zenodo;

use App\Helper\UrlHelper;
use DateTime;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ZenodoClient implements ZenodoClientInterface
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $zenodoClientApiEndpoint;

    /**
     * @var string
     */
    private $zenodoClientWriteAccessToken;

    public function __construct(
        HttpClientInterface $httpClient,
        string $zenodoClientApiEndpoint,
        string $zenodoClientWriteAccessToken
    ) {
        $this->httpClient = $httpClient;
        $this->zenodoClientApiEndpoint = $zenodoClientApiEndpoint;
        $this->zenodoClientWriteAccessToken = $zenodoClientWriteAccessToken;
    }

    public function getLatestDepositionIdVersion(string $recordId): string
    {
        $url = $this->zenodoClientApiEndpoint.'/records/'.$recordId;

        $response = $this->httpClient->request('GET', $url.$this->formatQueryParameters());

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_OK !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        $recordData = (array) json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return $recordData['metadata']['relations']['version'][0]['last_child']['pid_value'];
    }

    public function getRecord(string $recordId): array
    {
        $url = $this->zenodoClientApiEndpoint.'/records/'.$recordId;

        $response = $this->httpClient->request('GET', $url.$this->formatQueryParameters());

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_OK !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        return (array) json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    public function getDepositions(): array
    {
        $url = $this->zenodoClientApiEndpoint.'/deposit/depositions';

        $response = $this->httpClient->request('GET', $url.$this->formatQueryParameters());

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_OK !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        return (array) json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    public function getDeposition(string $depositionId): array
    {
        $url = $this->zenodoClientApiEndpoint.'/deposit/depositions/'.$depositionId;

        $response = $this->httpClient->request('GET', $url.$this->formatQueryParameters());

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_OK !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        return (array) json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    public function createDeposition(
        DateTime $publicationDate,
        string $title,
        string $description,
        array $keywords,
        array $communities,
        array $creators
    ): array {
        $url = $this->zenodoClientApiEndpoint.'/deposit/depositions';

        $response = $this->httpClient->request(
            'POST',
            $url.$this->formatQueryParameters(),
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
                    JSON_THROW_ON_ERROR,
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

        return (array) json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    public function publishDeposition(string $depositionId): array
    {
        $url = $this->zenodoClientApiEndpoint.'/deposit/depositions/'.$depositionId.'/actions/publish';

        $response = $this->httpClient->request(
            'POST',
            $url.$this->formatQueryParameters(),
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

        return (array) json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    public function newVersion(string $depositionId): string
    {
        $url = $this->zenodoClientApiEndpoint.'/deposit/depositions/'.$depositionId.'/actions/newversion';

        $response = $this->httpClient->request(
            'POST',
            $url.$this->formatQueryParameters(),
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

        $newVersionResponse = (array) json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        $parts = explode('/', $newVersionResponse['links']['latest_draft']);

        return $parts[\count($parts) - 1];
    }

    public function deleteVersion(string $depositionId): void
    {
        $url = $this->zenodoClientApiEndpoint.'/deposit/depositions/'.$depositionId;

        $response = $this->httpClient->request(
            'DELETE',
            $url.$this->formatQueryParameters(),
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
        $url = $this->zenodoClientApiEndpoint.'/deposit/depositions/'.$depositionId.'/files';

        $response = $this->httpClient->request('GET', $url.$this->formatQueryParameters());

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_OK !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        return (array) json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    public function saveFile(
        string $fileName,
        string $file,
        string $depositionId
    ): array {
        $formData = new FormDataPart(
            [
                'name' => $fileName,
                'file' => new DataPart($file, $fileName),
            ]
        );

        $url = $this->zenodoClientApiEndpoint.'/deposit/depositions/'.$depositionId.'/files';

        $response = $this->httpClient->request(
            'POST',
            $url.$this->formatQueryParameters(),
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

        $saveFileResponse = (array) json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if ($saveFileResponse['filename'] !== $fileName) {
            $message = sprintf('Illegal file name "%s", should be "%s"', $fileName, $saveFileResponse['filename']);
            throw new RuntimeException($message);
        }

        return $saveFileResponse;
    }

    public function removeFile(string $fileId, string $depositionId): void
    {
        $url = $this->zenodoClientApiEndpoint.'/deposit/depositions/'.$depositionId.'/files/'.$fileId;

        $response = $this->httpClient->request('DELETE', $url.$this->formatQueryParameters());

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

    private function formatQueryParameters(array $queryParameters = [])
    {
        return UrlHelper::formatQueryParameters(
            array_merge(
                [
                    'access_token' => $this->zenodoClientWriteAccessToken,
                ],
                $queryParameters
            )
        );
    }
}
