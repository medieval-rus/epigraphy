<?php

declare(strict_types=1);

namespace App\Services\Translation;

use App\Persistence\Entity\Content\Post;
use App\Persistence\Entity\Epigraphy\Inscription;
use App\Persistence\Entity\Epigraphy\Interpretation;
use App\Persistence\Entity\Epigraphy\LocalizedText;
use App\Persistence\Entity\Epigraphy\ZeroRow;
use Doctrine\ORM\EntityManagerInterface;

final class TranslationSourceResolver
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function resolve(string $targetType, int $targetId, string $field): ?string
    {
        if ($targetId <= 0) {
            return null;
        }

        switch ($targetType) {
            case LocalizedText::TARGET_INSCRIPTION:
                $entity = $this->entityManager->getRepository(Inscription::class)->find($targetId);
                if (!$entity instanceof Inscription) {
                    return null;
                }
                return $this->normalize($this->resolveInscriptionField($entity, $field));

            case LocalizedText::TARGET_ZERO_ROW:
                $entity = $this->entityManager->getRepository(ZeroRow::class)->find($targetId);
                if (!$entity instanceof ZeroRow) {
                    return null;
                }
                return $this->normalize($this->resolveZeroRowField($entity, $field));

            case LocalizedText::TARGET_INTERPRETATION:
                $entity = $this->entityManager->getRepository(Interpretation::class)->find($targetId);
                if (!$entity instanceof Interpretation) {
                    return null;
                }
                return $this->normalize($this->resolveInterpretationField($entity, $field));

            case LocalizedText::TARGET_POST:
                $entity = $this->entityManager->getRepository(Post::class)->find($targetId);
                if (!$entity instanceof Post) {
                    return null;
                }
                return $this->normalize($this->resolvePostField($entity, $field));

            default:
                return null;
        }
    }

    private function resolveInscriptionField(Inscription $inscription, string $field): ?string
    {
        switch ($field) {
            case 'dateExplanation':
                return $inscription->getDateExplanation();
            case 'comment':
                return $inscription->getComment();
            default:
                return null;
        }
    }

    private function resolveZeroRowField(ZeroRow $zeroRow, string $field): ?string
    {
        switch ($field) {
            case 'origin':
                return $zeroRow->getOrigin();
            case 'placeOnCarrier':
                return $zeroRow->getPlaceOnCarrier();
            case 'interpretationComment':
                return $zeroRow->getInterpretationComment();
            case 'text':
                return $zeroRow->getText();
            case 'transliteration':
                return $zeroRow->getTransliteration();
            case 'reconstruction':
                return $zeroRow->getReconstruction();
            case 'normalization':
                return $zeroRow->getNormalization();
            case 'translation':
                return $zeroRow->getTranslation();
            case 'description':
                return $zeroRow->getDescription();
            case 'dateInText':
                return $zeroRow->getDateInText();
            case 'nonStratigraphicalDate':
                return $zeroRow->getNonStratigraphicalDate();
            case 'historicalDate':
                return $zeroRow->getHistoricalDate();
            default:
                return null;
        }
    }

    private function resolveInterpretationField(Interpretation $interpretation, string $field): ?string
    {
        switch ($field) {
            case 'comment':
                return $interpretation->getComment();
            case 'origin':
                return $interpretation->getOrigin();
            case 'placeOnCarrier':
                return $interpretation->getPlaceOnCarrier();
            case 'interpretationComment':
                return $interpretation->getInterpretationComment();
            case 'text':
                return $interpretation->getText();
            case 'transliteration':
                return $interpretation->getTransliteration();
            case 'reconstruction':
                return $interpretation->getReconstruction();
            case 'normalization':
                return $interpretation->getNormalization();
            case 'translation':
                return $interpretation->getTranslation();
            case 'description':
                return $interpretation->getDescription();
            case 'dateInText':
                return $interpretation->getDateInText();
            case 'nonStratigraphicalDate':
                return $interpretation->getNonStratigraphicalDate();
            case 'historicalDate':
                return $interpretation->getHistoricalDate();
            default:
                return null;
        }
    }

    private function resolvePostField(Post $post, string $field): ?string
    {
        switch ($field) {
            case 'title':
                return $post->getTitle();
            case 'body':
                return $post->getBody();
            default:
                return null;
        }
    }

    private function normalize(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        return '' === trim($value) ? null : $value;
    }
}
