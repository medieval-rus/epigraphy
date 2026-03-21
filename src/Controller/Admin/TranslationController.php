<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Persistence\Entity\Epigraphy\LocalizedText;
use App\Services\Translation\YandexCloudTranslateClient;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

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

    public function store(
        Request $request,
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            $payload = $request->request->all();
        }

        $targetType = isset($payload['targetType']) && is_string($payload['targetType']) ? trim($payload['targetType']) : '';
        $targetId = isset($payload['targetId']) ? (int) $payload['targetId'] : 0;
        $field = isset($payload['field']) && is_string($payload['field']) ? trim($payload['field']) : '';
        $locale = isset($payload['locale']) && is_string($payload['locale']) ? strtolower(trim($payload['locale'])) : 'en';
        $value = isset($payload['value']) && is_string($payload['value']) ? trim($payload['value']) : '';
        $isAiGenerated = isset($payload['isAiGenerated']) && in_array((string) $payload['isAiGenerated'], ['1', 'true', 'on'], true);

        if ('' === $targetType || $targetId <= 0 || '' === $field) {
            return new JsonResponse(
                ['error' => 'targetType, targetId and field are required.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $csrfTokenValue = $request->headers->get('X-CSRF-Token');
        if (!is_string($csrfTokenValue) || '' === trim($csrfTokenValue)) {
            $csrfTokenValue = isset($payload['_token']) && is_string($payload['_token']) ? $payload['_token'] : '';
        }
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('admin_translation_store', $csrfTokenValue))) {
            return new JsonResponse(
                ['error' => 'Invalid CSRF token.'],
                Response::HTTP_FORBIDDEN
            );
        }

        $allowedFieldsByTargetType = [
            LocalizedText::TARGET_INSCRIPTION => [
                'dateExplanation',
                'comment',
            ],
            LocalizedText::TARGET_ZERO_ROW => [
                'origin',
                'placeOnCarrier',
                'interpretationComment',
                'text',
                'transliteration',
                'reconstruction',
                'normalization',
                'translation',
                'description',
                'dateInText',
                'nonStratigraphicalDate',
                'historicalDate',
            ],
            LocalizedText::TARGET_INTERPRETATION => [
                'comment',
                'origin',
                'placeOnCarrier',
                'interpretationComment',
                'text',
                'transliteration',
                'reconstruction',
                'normalization',
                'translation',
                'description',
                'dateInText',
                'nonStratigraphicalDate',
                'historicalDate',
            ],
        ];
        if (!array_key_exists($targetType, $allowedFieldsByTargetType)) {
            return new JsonResponse(
                ['error' => 'Unsupported targetType.'],
                Response::HTTP_BAD_REQUEST
            );
        }
        if (!in_array($field, $allowedFieldsByTargetType[$targetType], true)) {
            return new JsonResponse(
                ['error' => 'Unsupported field for targetType.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ('' === $locale) {
            $locale = 'en';
        }

        $repository = $entityManager->getRepository(LocalizedText::class);
        $localizedText = $repository->findOneBy(
            [
                'targetType' => $targetType,
                'targetId' => $targetId,
                'field' => $field,
                'locale' => $locale,
            ]
        );

        if ('' === $value) {
            if (null !== $localizedText) {
                $entityManager->remove($localizedText);
                $entityManager->flush();
            }

            return new JsonResponse(['stored' => false, 'removed' => true]);
        }

        if (null === $localizedText) {
            $localizedText = (new LocalizedText())
                ->setTargetType($targetType)
                ->setTargetId($targetId)
                ->setField($field)
                ->setLocale($locale);
            $entityManager->persist($localizedText);
        }

        $localizedText
            ->setValue($value)
            ->setIsAiGenerated($isAiGenerated);

        $entityManager->flush();

        return new JsonResponse(['stored' => true, 'removed' => false]);
    }
}
