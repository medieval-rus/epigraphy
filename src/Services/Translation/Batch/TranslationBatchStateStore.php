<?php

declare(strict_types=1);

namespace App\Services\Translation\Batch;

use RuntimeException;
use Symfony\Component\HttpKernel\KernelInterface;

final class TranslationBatchStateStore
{
    private string $directoryPath;
    private string $stateFilePath;
    private string $lockFilePath;

    public function __construct(KernelInterface $kernel)
    {
        $this->directoryPath = $kernel->getProjectDir().'/var/translation_batch';
        $this->stateFilePath = $this->directoryPath.'/state.json';
        $this->lockFilePath = $this->directoryPath.'/state.lock';
    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public function withExclusiveLock(callable $callback)
    {
        $this->ensureDirectoryExists();

        $lockHandle = fopen($this->lockFilePath, 'c+');
        if (false === $lockHandle) {
            throw new RuntimeException('Failed to open translation batch lock file.');
        }

        if (!flock($lockHandle, LOCK_EX)) {
            fclose($lockHandle);
            throw new RuntimeException('Failed to acquire translation batch lock.');
        }

        try {
            return $callback();
        } finally {
            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);
        }
    }

    public function readState(): ?array
    {
        if (!is_file($this->stateFilePath)) {
            return null;
        }

        $contents = file_get_contents($this->stateFilePath);
        if (false === $contents || '' === trim($contents)) {
            return null;
        }

        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : null;
    }

    public function writeState(array $state): void
    {
        $this->ensureDirectoryExists();

        $json = json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (false === $json) {
            throw new RuntimeException('Failed to encode translation batch state.');
        }

        $result = file_put_contents($this->stateFilePath, $json);
        if (false === $result) {
            throw new RuntimeException('Failed to write translation batch state.');
        }
    }

    private function ensureDirectoryExists(): void
    {
        if (is_dir($this->directoryPath)) {
            return;
        }

        if (!mkdir($concurrentDirectory = $this->directoryPath, 0775, true) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException('Failed to create translation batch directory.');
        }
    }
}
