<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Services\Translation\Batch\TranslationBatchManager;
use RuntimeException;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Throwable;

final class TranslationBatchController extends CRUDController
{
    public function listAction(Request $request): Response
    {
        return $this->redirect($this->admin->generateUrl('manage'));
    }

    public function manageAction(
        TranslationBatchManager $batchManager,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->renderWithExtraParams(
            'admin/translation_batch/manage.html.twig',
            [
                'initialStatus' => $batchManager->status(),
                'csrfToken' => $csrfTokenManager->getToken('admin_translation_batch_control')->getValue(),
            ]
        );
    }

    public function startAction(
        Request $request,
        TranslationBatchManager $batchManager,
        CsrfTokenManagerInterface $csrfTokenManager
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$request->isMethod('POST')) {
            return new JsonResponse(['error' => 'Method not allowed.'], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $payload = $this->extractPayload($request);
        if (!$this->isBatchCsrfTokenValid($payload, $request, $csrfTokenManager)) {
            return new JsonResponse(['error' => 'Invalid CSRF token.'], Response::HTTP_FORBIDDEN);
        }

        $fromNumber = isset($payload['fromNumber']) ? (int) $payload['fromNumber'] : 0;
        $toNumber = isset($payload['toNumber']) ? (int) $payload['toNumber'] : 0;
        $overwrite = isset($payload['overwrite']) && in_array((string) $payload['overwrite'], ['1', 'true', 'on'], true);

        if ($fromNumber <= 0 || $toNumber <= 0 || $fromNumber > $toNumber) {
            return new JsonResponse(
                ['error' => 'Invalid range: fromNumber and toNumber must be positive and fromNumber <= toNumber.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            return new JsonResponse($batchManager->start($fromNumber, $toNumber, $overwrite));
        } catch (RuntimeException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $exception) {
            return new JsonResponse(['error' => 'Failed to start translation batch.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function statusAction(TranslationBatchManager $batchManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return new JsonResponse($batchManager->status());
    }

    public function tickAction(
        Request $request,
        TranslationBatchManager $batchManager,
        CsrfTokenManagerInterface $csrfTokenManager
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$request->isMethod('POST')) {
            return new JsonResponse(['error' => 'Method not allowed.'], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $payload = $this->extractPayload($request);
        if (!$this->isBatchCsrfTokenValid($payload, $request, $csrfTokenManager)) {
            return new JsonResponse(['error' => 'Invalid CSRF token.'], Response::HTTP_FORBIDDEN);
        }

        $limit = isset($payload['limit']) ? (int) $payload['limit'] : 20;

        return new JsonResponse($batchManager->tick($limit));
    }

    public function cancelAction(
        Request $request,
        TranslationBatchManager $batchManager,
        CsrfTokenManagerInterface $csrfTokenManager
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$request->isMethod('POST')) {
            return new JsonResponse(['error' => 'Method not allowed.'], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $payload = $this->extractPayload($request);
        if (!$this->isBatchCsrfTokenValid($payload, $request, $csrfTokenManager)) {
            return new JsonResponse(['error' => 'Invalid CSRF token.'], Response::HTTP_FORBIDDEN);
        }

        return new JsonResponse($batchManager->cancel());
    }

    private function extractPayload(Request $request): array
    {
        $payload = json_decode($request->getContent(), true);
        if (is_array($payload)) {
            return $payload;
        }

        $formPayload = $request->request->all();

        return is_array($formPayload) ? $formPayload : [];
    }

    private function isBatchCsrfTokenValid(
        array $payload,
        Request $request,
        CsrfTokenManagerInterface $csrfTokenManager
    ): bool {
        $csrfTokenValue = $request->headers->get('X-CSRF-Token');
        if (!is_string($csrfTokenValue) || '' === trim($csrfTokenValue)) {
            $csrfTokenValue = isset($payload['_token']) && is_string($payload['_token']) ? $payload['_token'] : '';
        }

        if ('' === trim($csrfTokenValue)) {
            return false;
        }

        return $csrfTokenManager->isTokenValid(new CsrfToken('admin_translation_batch_control', $csrfTokenValue));
    }
}
