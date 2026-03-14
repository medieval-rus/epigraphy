<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Services\Translation\YandexCloudTranslateClient;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TranslationController extends AbstractController
{
    public function preview(Request $request, YandexCloudTranslateClient $translateClient): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            $payload = $request->request->all();
        }

        $text = isset($payload['text']) && is_string($payload['text']) ? $payload['text'] : '';
        $sourceLang = isset($payload['sourceLang']) && is_string($payload['sourceLang']) ? $payload['sourceLang'] : 'ru';
        $targetLang = isset($payload['targetLang']) && is_string($payload['targetLang']) ? $payload['targetLang'] : 'en';

        if ('' === trim($text)) {
            return new JsonResponse(['error' => 'Text is required.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $translatedText = $translateClient->translateHtml($text, $sourceLang, $targetLang);
        } catch (RuntimeException $exception) {
            return new JsonResponse(
                ['error' => $exception->getMessage()],
                Response::HTTP_BAD_GATEWAY
            );
        }

        return new JsonResponse(
            [
                'translatedText' => $translatedText,
                'sourceLang' => strtolower($sourceLang),
                'targetLang' => strtolower($targetLang),
            ]
        );
    }
}
