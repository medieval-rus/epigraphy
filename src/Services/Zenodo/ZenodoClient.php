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
use Psr\Log\LoggerInterface;
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
    private $zenodoApiEndpoint;

    /**
     * @var string
     */
    private $zenodoAccessToken;

    public function __construct(
        LoggerInterface $logger,
        HttpClientInterface $httpClient,
        string $zenodoApiEndpoint,
        string $zenodoAccessToken
    ) {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->zenodoApiEndpoint = $zenodoApiEndpoint;
        $this->zenodoAccessToken = $zenodoAccessToken;
    }

    public function createImagesDeposition(
        string $title,
        string $description,
        array $keywords,
        array $communities,
        array $creators
    ): array {
        $queryParameters = UrlHelper::formatQueryParameters(
            [
                'access_token' => $this->zenodoAccessToken,
            ]
        );

        $response = $this->httpClient->request(
            'POST',
            $this->zenodoApiEndpoint.'/deposit/depositions'.$queryParameters,
            [
                'body' => json_encode(
                    [
                        'metadata' => [
                            'upload_type' => 'image',
                            'image_type' => 'other',
                            'access_right' => 'open',
                            'communities' => $communities,
                            'creators' => $creators,
                            'title' => $title,
                            'description' => $description,
                            'publication_date' => '2020-09-01',
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

        $info = (array) $response->getInfo();
        $url = $info['url'];
        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_CREATED !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        return (array) json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    public function saveFile(string $fileName, string $file, string $depositionId): string
    {
        $queryParameters = UrlHelper::formatQueryParameters(
            [
                'access_token' => $this->zenodoAccessToken,
            ]
        );

        $formData = new FormDataPart(
            [
                'name' => $fileName,
                'file' => new DataPart($file, $fileName),
            ]
        );

        $response = $this->httpClient->request(
            'POST',
            $this->zenodoApiEndpoint.'/deposit/depositions/'.$depositionId.'/files'.$queryParameters,
            [
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToString(),
            ]
        );

        $info = (array) $response->getInfo();
        $url = $info['url'];
        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_CREATED !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        $uploadFileResponse = (array) json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $uploadFileResponse['links']['download'];
    }

    public function publishDeposition(string $depositionId): void
    {
        $queryParameters = UrlHelper::formatQueryParameters(
            [
                'access_token' => $this->zenodoAccessToken,
            ]
        );

        $response = $this->httpClient->request(
            'POST',
            $this->zenodoApiEndpoint.'/deposit/depositions/'.$depositionId.'/actions/publish'.$queryParameters,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        $info = (array) $response->getInfo();
        $url = $info['url'];
        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_ACCEPTED !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }
    }

    public function newVersion(string $depositionId): string
    {
        $queryParameters = UrlHelper::formatQueryParameters(
            [
                'access_token' => $this->zenodoAccessToken,
            ]
        );

        $response = $this->httpClient->request(
            'POST',
            $this->zenodoApiEndpoint.'/deposit/depositions/'.$depositionId.'/actions/newversion'.$queryParameters,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        $info = (array) $response->getInfo();
        $url = $info['url'];
        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if (Response::HTTP_CREATED !== $statusCode) {
            $message = sprintf('Request to "%s" failed with status code "%d": %s', $url, $statusCode, $content);
            throw new RuntimeException($message);
        }

        $newVersionResponse = (array) json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $parts = explode('/', $newVersionResponse['links']['latest_draft']);

        return $parts[\count($parts) - 1];
    }

    public function createAndPublishImagesDeposition(): string
    {
        $deposition = $this->createImagesDeposition(
            'Test deposition of '.(new \DateTime())->format('Y-m-d H:i:s'),
            'Images for the «Epigraphy of Medieval Rus\'» database',
            ['keyword1', 'keyword12'],
            [['identifier' => 'medieval-rus-epigraphy']],
            [
                [
                    'name' => 'Alexey Gippius',
                    'affiliation' => 'Project curator',
                    'orcid' => '0000-0001-7797-9446',
                ],
                [
                    'name' => 'Anton Dyshkant',
                    'affiliation' => 'Project developer',
                    'orcid' => '0000-0002-6159-3263',
                ],
            ]
        );

        $depositionId = (string) $deposition['id'];

        $this->saveFile(
            'README',
            'This repository contains images for the «Epigraphy of Medieval Rus\'» database',
            $depositionId
        );

        $this->publishDeposition($depositionId);

        return $depositionId;
    }
}
