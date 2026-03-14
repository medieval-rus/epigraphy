<?php

declare(strict_types=1);

namespace App\Services\Translation;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class YandexCloudTranslateClient
{
    private HttpClientInterface $httpClient;
    private string $endpoint;
    private string $apiKey;
    private string $folderId;
    private int $timeoutSeconds;

    public function __construct(
        HttpClientInterface $httpClient,
        string $endpoint,
        string $apiKey,
        string $folderId,
        int $timeoutSeconds
    ) {
        $this->httpClient = $httpClient;
        $this->endpoint = $endpoint;
        $this->apiKey = $apiKey;
        $this->folderId = $folderId;
        $this->timeoutSeconds = $timeoutSeconds;
    }

    public function translateHtml(string $text, string $sourceLanguageCode, string $targetLanguageCode): string
    {
        $trimmedText = trim($text);
        if ('' === $trimmedText) {
            throw new RuntimeException('Source text is empty.');
        }

        if (mb_strlen($trimmedText) > 10000) {
            throw new RuntimeException('Source text exceeds 10000 characters limit.');
        }

        if ('' === trim($this->apiKey) || '' === trim($this->folderId)) {
            throw new RuntimeException('Yandex Translate credentials are not configured.');
        }

        try {
            $response = $this->httpClient->request(
                'POST',
                $this->endpoint,
                [
                    'headers' => [
                        'Authorization' => 'Api-Key '.$this->apiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'folderId' => $this->folderId,
                        'texts' => [$trimmedText],
                        'sourceLanguageCode' => strtolower($sourceLanguageCode),
                        'targetLanguageCode' => strtolower($targetLanguageCode),
                        'format' => 'HTML',
                    ],
                    'timeout' => $this->timeoutSeconds,
                ]
            );

            $statusCode = $response->getStatusCode();
            $rawContent = $response->getContent(false);

            if (200 !== $statusCode) {
                throw new RuntimeException(sprintf('Yandex Translate request failed with status %d.', $statusCode));
            }

            $payload = json_decode($rawContent, true);
            if (!is_array($payload) || !isset($payload['translations'][0]['text']) || !is_string($payload['translations'][0]['text'])) {
                throw new RuntimeException('Unexpected response from Yandex Translate.');
            }

            return $payload['translations'][0]['text'];
        } catch (ExceptionInterface $exception) {
            throw new RuntimeException('Failed to call Yandex Translate.', 0, $exception);
        }
    }
}
