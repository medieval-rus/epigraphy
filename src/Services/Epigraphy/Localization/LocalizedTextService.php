<?php

declare(strict_types=1);

namespace App\Services\Epigraphy\Localization;

use App\Persistence\Entity\Epigraphy\LocalizedText;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class LocalizedTextService
{
    private const BASE_CONTENT_LOCALE = 'ru';

    private EntityManagerInterface $entityManager;
    private RequestStack $requestStack;

    /** @var array<string, ?LocalizedText> */
    private array $entityCache = [];

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    public function resolveForEntity(
        $entity,
        string $field,
        ?string $fallbackValue = null,
        ?string $locale = null,
        bool $allowRuFallback = true
    ): ?string
    {
        $targetType = LocalizedText::resolveTargetTypeFromEntity($entity);
        if (null === $targetType) {
            return $fallbackValue;
        }

        if (!method_exists($entity, 'getId')) {
            return $fallbackValue;
        }

        $targetId = $entity->getId();
        if (null === $targetId) {
            return $fallbackValue;
        }

        $request = $this->requestStack->getCurrentRequest();
        $normalizedLocale = $this->normalizeLocale($locale ?? (null === $request ? 'ru' : $request->getLocale()));

        // For base content locale we prefer mapped entity fields to avoid stale
        // values from historic ru records in localized_text.
        if (self::BASE_CONTENT_LOCALE === $normalizedLocale) {
            return $fallbackValue;
        }

        $primaryValue = $this->findValue($targetType, (int) $targetId, $field, $normalizedLocale);
        if (null !== $primaryValue) {
            return $primaryValue;
        }

        return $fallbackValue;
    }

    public function upsert(string $targetType, int $targetId, string $field, string $locale, ?string $value): void
    {
        $normalizedLocale = $this->normalizeLocale($locale);
        $normalizedValue = null === $value ? null : trim($value);

        $entity = $this->entityManager->getRepository(LocalizedText::class)->findOneBy(
            [
                'targetType' => $targetType,
                'targetId' => $targetId,
                'field' => $field,
                'locale' => $normalizedLocale,
            ]
        );

        if (null === $normalizedValue || '' === $normalizedValue) {
            if (null !== $entity) {
                $this->entityManager->remove($entity);
            }
            $this->entityCache[$this->cacheKey($targetType, $targetId, $field, $normalizedLocale)] = null;
            return;
        }

        if (null === $entity) {
            $entity = (new LocalizedText())
                ->setTargetType($targetType)
                ->setTargetId($targetId)
                ->setField($field)
                ->setLocale($normalizedLocale)
                ->setValue($normalizedValue);
            $this->entityManager->persist($entity);
        } else {
            $entity->setValue($normalizedValue);
        }

        $this->entityCache[$this->cacheKey($targetType, $targetId, $field, $normalizedLocale)] = $entity;
    }

    public function isAiGeneratedForEntity($entity, string $field, ?string $locale = null): bool
    {
        $targetType = LocalizedText::resolveTargetTypeFromEntity($entity);
        if (null === $targetType || !method_exists($entity, 'getId')) {
            return false;
        }

        $targetId = $entity->getId();
        if (null === $targetId) {
            return false;
        }

        $request = $this->requestStack->getCurrentRequest();
        $normalizedLocale = $this->normalizeLocale($locale ?? (null === $request ? 'ru' : $request->getLocale()));
        $localizedEntity = $this->findEntity($targetType, (int) $targetId, $field, $normalizedLocale);

        if (null === $localizedEntity) {
            return false;
        }

        return $localizedEntity->isAiGenerated();
    }

    private function findValue(string $targetType, int $targetId, string $field, string $locale): ?string
    {
        $entity = $this->findEntity($targetType, $targetId, $field, $locale);
        return null === $entity ? null : $entity->getValue();
    }

    private function findEntity(string $targetType, int $targetId, string $field, string $locale): ?LocalizedText
    {
        $cacheKey = $this->cacheKey($targetType, $targetId, $field, $locale);
        if (array_key_exists($cacheKey, $this->entityCache)) {
            return $this->entityCache[$cacheKey];
        }

        $entity = $this->entityManager->getRepository(LocalizedText::class)->findOneBy(
            [
                'targetType' => $targetType,
                'targetId' => $targetId,
                'field' => $field,
                'locale' => $locale,
            ]
        );
        $this->entityCache[$cacheKey] = $entity;

        return $entity;
    }

    private function normalizeLocale(?string $locale): string
    {
        $normalizedLocale = strtolower((string) ($locale ?? 'ru'));
        if (false !== strpos($normalizedLocale, '_')) {
            $normalizedLocale = substr($normalizedLocale, 0, (int) strpos($normalizedLocale, '_'));
        }
        if (false !== strpos($normalizedLocale, '-')) {
            $normalizedLocale = substr($normalizedLocale, 0, (int) strpos($normalizedLocale, '-'));
        }
        if ('' === $normalizedLocale) {
            return 'ru';
        }

        return $normalizedLocale;
    }

    private function cacheKey(string $targetType, int $targetId, string $field, string $locale): string
    {
        return $targetType.'|'.$targetId.'|'.$field.'|'.$locale;
    }
}
