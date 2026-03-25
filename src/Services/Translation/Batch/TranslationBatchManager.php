<?php

declare(strict_types=1);

namespace App\Services\Translation\Batch;

use App\Persistence\Entity\Epigraphy\Inscription;
use App\Persistence\Entity\Epigraphy\Interpretation;
use App\Persistence\Entity\Epigraphy\LocalizedText;
use App\Services\Translation\TranslationSourceResolver;
use App\Services\Translation\YandexCloudTranslateClient;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Throwable;

final class TranslationBatchManager
{
    /** @var string[] */
    private const INSCRIPTION_FIELDS = [
        'dateExplanation',
        'comment',
    ];

    /** @var string[] */
    private const ZERO_ROW_FIELDS = [
        'origin',
        'placeOnCarrier',
        'interpretationComment',
        'translation',
        'description',
        'dateInText',
        'nonStratigraphicalDate',
        'historicalDate',
    ];

    /** @var string[] */
    private const INTERPRETATION_FIELDS = [
        'comment',
        'origin',
        'placeOnCarrier',
        'interpretationComment',
        'translation',
        'description',
        'dateInText',
        'nonStratigraphicalDate',
        'historicalDate',
    ];

    private EntityManagerInterface $entityManager;
    private TranslationSourceResolver $translationSourceResolver;
    private YandexCloudTranslateClient $translateClient;
    private TranslationBatchStateStore $stateStore;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslationSourceResolver $translationSourceResolver,
        YandexCloudTranslateClient $translateClient,
        TranslationBatchStateStore $stateStore
    ) {
        $this->entityManager = $entityManager;
        $this->translationSourceResolver = $translationSourceResolver;
        $this->translateClient = $translateClient;
        $this->stateStore = $stateStore;
    }

    public function start(int $fromNumber, int $toNumber, bool $overwrite): array
    {
        if ($fromNumber <= 0 || $toNumber <= 0 || $fromNumber > $toNumber) {
            throw new RuntimeException('Invalid inscription number range.');
        }

        return $this->stateStore->withExclusiveLock(function () use ($fromNumber, $toNumber, $overwrite): array {
            $existingState = $this->stateStore->readState();
            if (is_array($existingState) && ('running' === ($existingState['status'] ?? null))) {
                throw new RuntimeException('Another translation batch is already running.');
            }

            $selection = $this->buildSelectionByIdRange($fromNumber, $toNumber);
            $now = time();

            $state = [
                'jobId' => (string) $now.'-'.bin2hex(random_bytes(6)),
                'status' => 0 === count($selection['entries']) ? 'done' : 'running',
                'createdAt' => $now,
                'updatedAt' => $now,
                'startedAt' => $now,
                'finishedAt' => 0 === count($selection['entries']) ? $now : null,
                'fromNumber' => $fromNumber,
                'toNumber' => $toNumber,
                'overwrite' => $overwrite,
                'entries' => $selection['entries'],
                'totalInscriptions' => count($selection['entries']),
                'totalUnits' => $selection['totalUnits'],
                'nonNumericInscriptions' => $selection['nonNumericInscriptions'],
                'progress' => [
                    'inscriptionIndex' => 0,
                    'unitIndex' => 0,
                    'processedInscriptions' => 0,
                    'processedUnits' => 0,
                    'translated' => 0,
                    'skipped' => 0,
                    'errors' => 0,
                    'currentInscriptionId' => null,
                    'currentInscriptionNumber' => null,
                    'lastError' => null,
                ],
            ];

            $this->stateStore->writeState($state);

            return $this->formatStateForClient($state);
        });
    }

    public function status(): array
    {
        return $this->stateStore->withExclusiveLock(function (): array {
            $state = $this->stateStore->readState();
            if (!is_array($state)) {
                return $this->formatStateForClient(null);
            }

            return $this->formatStateForClient($state);
        });
    }

    public function cancel(): array
    {
        return $this->stateStore->withExclusiveLock(function (): array {
            $state = $this->stateStore->readState();
            if (!is_array($state)) {
                return $this->formatStateForClient(null);
            }

            if ('running' === ($state['status'] ?? null)) {
                $state['status'] = 'canceled';
                $state['updatedAt'] = time();
                $state['finishedAt'] = time();
                $state['progress']['currentInscriptionId'] = null;
                $state['progress']['currentInscriptionNumber'] = null;
                $this->stateStore->writeState($state);
            }

            return $this->formatStateForClient($state);
        });
    }

    public function tick(int $limit): array
    {
        $safeLimit = max(1, min($limit, 50));

        return $this->stateStore->withExclusiveLock(function () use ($safeLimit): array {
            $state = $this->stateStore->readState();
            if (!is_array($state)) {
                return $this->formatStateForClient(null);
            }

            if ('running' !== ($state['status'] ?? null)) {
                return $this->formatStateForClient($state);
            }

            $localizedTextRepository = $this->entityManager->getRepository(LocalizedText::class);
            $inscriptionRepository = $this->entityManager->getRepository(Inscription::class);

            $processedThisTick = 0;
            $entries = isset($state['entries']) && is_array($state['entries']) ? $state['entries'] : [];
            $entryCount = count($entries);

            try {
                while (
                    $processedThisTick < $safeLimit
                    && isset($state['progress']['inscriptionIndex'])
                    && (int) $state['progress']['inscriptionIndex'] < $entryCount
                ) {
                    $inscriptionIndex = (int) $state['progress']['inscriptionIndex'];
                    $unitIndex = (int) ($state['progress']['unitIndex'] ?? 0);
                    $entry = $entries[$inscriptionIndex];

                    $state['progress']['currentInscriptionId'] = (int) ($entry['id'] ?? 0);
                    $state['progress']['currentInscriptionNumber'] = (string) ($entry['number'] ?? '');

                    $inscription = $inscriptionRepository->find((int) ($entry['id'] ?? 0));
                    if (!$inscription instanceof Inscription) {
                        $remainingUnits = $this->calculateEntryUnits($entry);
                        $state['progress']['processedUnits'] += max($remainingUnits - $unitIndex, 0);
                        $state['progress']['skipped'] += max($remainingUnits - $unitIndex, 0);
                        $state['progress']['processedInscriptions']++;
                        $state['progress']['inscriptionIndex']++;
                        $state['progress']['unitIndex'] = 0;
                        continue;
                    }

                    $units = $this->buildUnitsForInscription($inscription);
                    $unitsCount = count($units);

                    while ($unitIndex < $unitsCount && $processedThisTick < $safeLimit) {
                        $unit = $units[$unitIndex];
                        $result = $this->processUnit(
                            $localizedTextRepository,
                            $unit,
                            (bool) ($state['overwrite'] ?? false)
                        );

                        $state['progress']['processedUnits']++;
                        if ('translated' === $result) {
                            $state['progress']['translated']++;
                        } elseif ('skipped' === $result) {
                            $state['progress']['skipped']++;
                        } else {
                            $state['progress']['errors']++;
                        }

                        $unitIndex++;
                        $processedThisTick++;
                    }

                    if ($unitIndex >= $unitsCount) {
                        $state['progress']['processedInscriptions']++;
                        $state['progress']['inscriptionIndex']++;
                        $state['progress']['unitIndex'] = 0;
                    } else {
                        $state['progress']['unitIndex'] = $unitIndex;
                    }
                }

                $this->entityManager->flush();
                $this->entityManager->clear();
            } catch (Throwable $exception) {
                $state['status'] = 'failed';
                $state['finishedAt'] = time();
                $state['progress']['lastError'] = $exception->getMessage();
                $state['progress']['currentInscriptionId'] = null;
                $state['progress']['currentInscriptionNumber'] = null;
                $state['updatedAt'] = time();
                $this->stateStore->writeState($state);

                return $this->formatStateForClient($state);
            }

            if ((int) ($state['progress']['inscriptionIndex'] ?? 0) >= $entryCount) {
                $state['status'] = 'done';
                $state['finishedAt'] = time();
                $state['progress']['currentInscriptionId'] = null;
                $state['progress']['currentInscriptionNumber'] = null;
            }

            $state['updatedAt'] = time();
            $this->stateStore->writeState($state);

            return $this->formatStateForClient($state);
        });
    }

    private function processUnit(
        $localizedTextRepository,
        array $unit,
        bool $overwrite
    ): string {
        $targetType = (string) ($unit['targetType'] ?? '');
        $targetId = (int) ($unit['targetId'] ?? 0);
        $field = (string) ($unit['field'] ?? '');
        $locale = 'en';

        if ('' === $targetType || $targetId <= 0 || '' === $field) {
            return 'skipped';
        }

        $criteria = [
            'targetType' => $targetType,
            'targetId' => $targetId,
            'field' => $field,
            'locale' => $locale,
        ];

        $localizedText = $localizedTextRepository->findOneBy($criteria);
        if (!$overwrite && $localizedText instanceof LocalizedText && '' !== trim((string) $localizedText->getValue())) {
            return 'skipped';
        }

        $sourceText = $this->translationSourceResolver->resolve($targetType, $targetId, $field);
        if (null === $sourceText) {
            return 'skipped';
        }

        try {
            $translatedText = $this->translateClient->translateHtml($sourceText, 'ru', 'en');
        } catch (RuntimeException $exception) {
            return 'error';
        }

        if (!$localizedText instanceof LocalizedText) {
            $localizedText = (new LocalizedText())
                ->setTargetType($targetType)
                ->setTargetId($targetId)
                ->setField($field)
                ->setLocale($locale);
            $this->entityManager->persist($localizedText);
        }

        $localizedText
            ->setValue($translatedText)
            ->setIsAiGenerated(true);

        return 'translated';
    }

    /**
     * @return array<int, array<string, int|string|bool>>
     */
    private function buildUnitsForInscription(Inscription $inscription): array
    {
        $units = [];

        $inscriptionId = $inscription->getId();
        if (null !== $inscriptionId) {
            foreach (self::INSCRIPTION_FIELDS as $field) {
                $units[] = [
                    'targetType' => LocalizedText::TARGET_INSCRIPTION,
                    'targetId' => $inscriptionId,
                    'field' => $field,
                ];
            }
        }

        $zeroRow = $inscription->getZeroRow();
        if (null !== $zeroRow && null !== $zeroRow->getId()) {
            foreach (self::ZERO_ROW_FIELDS as $field) {
                $units[] = [
                    'targetType' => LocalizedText::TARGET_ZERO_ROW,
                    'targetId' => (int) $zeroRow->getId(),
                    'field' => $field,
                ];
            }
        }

        $interpretations = $inscription->getInterpretations()->toArray();
        usort(
            $interpretations,
            static function (Interpretation $a, Interpretation $b): int {
                return ((int) $a->getId()) <=> ((int) $b->getId());
            }
        );

        foreach ($interpretations as $interpretation) {
            if (null === $interpretation->getId()) {
                continue;
            }

            foreach (self::INTERPRETATION_FIELDS as $field) {
                $units[] = [
                    'targetType' => LocalizedText::TARGET_INTERPRETATION,
                    'targetId' => (int) $interpretation->getId(),
                    'field' => $field,
                ];
            }
        }

        return $units;
    }

    /**
     * @return array{entries: array<int, array<string, int|string|bool>>, totalUnits: int, nonNumericInscriptions: int}
     */
    private function buildSelectionByIdRange(int $fromId, int $toId): array
    {
        $rows = $this->entityManager->createQueryBuilder()
            ->select('i.id AS id, i.number AS number, zr.id AS zeroRowId, COUNT(interp.id) AS interpretationCount')
            ->from(Inscription::class, 'i')
            ->leftJoin('i.zeroRow', 'zr')
            ->leftJoin('i.interpretations', 'interp')
            ->where('i.id >= :fromId')
            ->andWhere('i.id <= :toId')
            ->setParameter('fromId', $fromId)
            ->setParameter('toId', $toId)
            ->groupBy('i.id, i.number, zr.id')
            ->getQuery()
            ->getArrayResult();

        $entries = [];
        $totalUnits = 0;
        $nonNumericInscriptions = 0;

        foreach ($rows as $row) {
            $hasZeroRow = null !== ($row['zeroRowId'] ?? null);
            $interpretationCount = isset($row['interpretationCount']) ? (int) $row['interpretationCount'] : 0;

            $entry = [
                'id' => (int) $row['id'],
                'number' => (string) ($row['number'] ?? ''),
                'hasZeroRow' => $hasZeroRow,
                'interpretationCount' => $interpretationCount,
            ];

            $entries[] = $entry;
            $totalUnits += $this->calculateEntryUnits($entry);
        }

        usort(
            $entries,
            static function (array $a, array $b): int {
                return ((int) $a['id']) <=> ((int) $b['id']);
            }
        );

        return [
            'entries' => $entries,
            'totalUnits' => $totalUnits,
            'nonNumericInscriptions' => $nonNumericInscriptions,
        ];
    }

    /**
     * @param array<string, int|string|bool> $entry
     */
    private function calculateEntryUnits(array $entry): int
    {
        $units = count(self::INSCRIPTION_FIELDS);
        if (!empty($entry['hasZeroRow'])) {
            $units += count(self::ZERO_ROW_FIELDS);
        }

        $units += ((int) ($entry['interpretationCount'] ?? 0)) * count(self::INTERPRETATION_FIELDS);

        return $units;
    }

    private function formatStateForClient(?array $state): array
    {
        if (!is_array($state)) {
            return [
                'status' => 'idle',
                'jobId' => null,
                'fromNumber' => null,
                'toNumber' => null,
                'overwrite' => false,
                'totalInscriptions' => 0,
                'totalUnits' => 0,
                'nonNumericInscriptions' => 0,
                'processedInscriptions' => 0,
                'processedUnits' => 0,
                'translated' => 0,
                'skipped' => 0,
                'errors' => 0,
                'progressPercent' => 0,
                'currentInscriptionId' => null,
                'currentInscriptionNumber' => null,
                'lastError' => null,
                'createdAt' => null,
                'updatedAt' => null,
                'startedAt' => null,
                'finishedAt' => null,
            ];
        }

        $totalUnits = max((int) ($state['totalUnits'] ?? 0), 0);
        $processedUnits = max((int) ($state['progress']['processedUnits'] ?? 0), 0);

        $progressPercent = 0;
        if ($totalUnits > 0) {
            $progressPercent = (int) floor(($processedUnits / $totalUnits) * 100);
            $progressPercent = max(min($progressPercent, 100), 0);
        }

        return [
            'status' => (string) ($state['status'] ?? 'idle'),
            'jobId' => $state['jobId'] ?? null,
            'fromNumber' => $state['fromNumber'] ?? null,
            'toNumber' => $state['toNumber'] ?? null,
            'overwrite' => (bool) ($state['overwrite'] ?? false),
            'totalInscriptions' => (int) ($state['totalInscriptions'] ?? 0),
            'totalUnits' => $totalUnits,
            'nonNumericInscriptions' => (int) ($state['nonNumericInscriptions'] ?? 0),
            'processedInscriptions' => (int) ($state['progress']['processedInscriptions'] ?? 0),
            'processedUnits' => $processedUnits,
            'translated' => (int) ($state['progress']['translated'] ?? 0),
            'skipped' => (int) ($state['progress']['skipped'] ?? 0),
            'errors' => (int) ($state['progress']['errors'] ?? 0),
            'progressPercent' => $progressPercent,
            'currentInscriptionId' => $state['progress']['currentInscriptionId'] ?? null,
            'currentInscriptionNumber' => $state['progress']['currentInscriptionNumber'] ?? null,
            'lastError' => $state['progress']['lastError'] ?? null,
            'createdAt' => $state['createdAt'] ?? null,
            'updatedAt' => $state['updatedAt'] ?? null,
            'startedAt' => $state['startedAt'] ?? null,
            'finishedAt' => $state['finishedAt'] ?? null,
        ];
    }
}
