<?php

declare(strict_types=1);

namespace App\Services\Epigraphy\Localization;

use App\Persistence\Entity\Epigraphy\LocalizedText;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class LocalizedTextService
{
    private EntityManagerInterface $entityManager;
    private RequestStack $requestStack;

    /** @var array<string, ?string> */
    private array $cache = [];

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    public function resolveForEntity($entity, string $field, ?string $fallbackValue = null, ?string $locale = null): ?string
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
        $primaryValue = $this->findValue($targetType, (int) $targetId, $field, $normalizedLocale);
        if (null !== $primaryValue) {
            return $primaryValue;
        }

        if ('ru' !== $normalizedLocale) {
            $ruValue = $this->findValue($targetType, (int) $targetId, $field, 'ru');
            if (null !== $ruValue) {
                return $ruValue;
            }
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
            $this->cache[$this->cacheKey($targetType, $targetId, $field, $normalizedLocale)] = null;
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

        $this->cache[$this->cacheKey($targetType, $targetId, $field, $normalizedLocale)] = $normalizedValue;
    }

    private function findValue(string $targetType, int $targetId, string $field, string $locale): ?string
    {
        $cacheKey = $this->cacheKey($targetType, $targetId, $field, $locale);
        if (array_key_exists($cacheKey, $this->cache)) {
            return $this->cache[$cacheKey];
        }

        $entity = $this->entityManager->getRepository(LocalizedText::class)->findOneBy(
            [
                'targetType' => $targetType,
                'targetId' => $targetId,
                'field' => $field,
                'locale' => $locale,
            ]
        );

        $value = null === $entity ? null : $entity->getValue();
        $this->cache[$cacheKey] = $value;

        return $value;
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
