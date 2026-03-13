<?php

declare(strict_types=1);

namespace App\Services\Epidoc\Xslt;

use InvalidArgumentException;

final class SaxonRuntimeConfig
{
    private string $projectDir;
    private string $javaBinary;
    private string $saxonJarPath;
    private string $stylesheetsDir;
    private int $timeoutSeconds;

    public function __construct(
        string $projectDir,
        string $javaBinary,
        string $saxonJarPath,
        string $stylesheetsDir,
        int $timeoutSeconds
    ) {
        $projectDir = rtrim($projectDir, DIRECTORY_SEPARATOR);
        if ($projectDir === '') {
            throw new InvalidArgumentException('Project directory must not be empty.');
        }
        if ($timeoutSeconds <= 0) {
            throw new InvalidArgumentException('Saxon timeout must be a positive integer.');
        }

        $this->projectDir = $projectDir;
        $this->javaBinary = trim($javaBinary) !== '' ? trim($javaBinary) : 'java';
        $this->saxonJarPath = $this->resolvePath($saxonJarPath);
        $this->stylesheetsDir = $this->resolvePath($stylesheetsDir);
        $this->timeoutSeconds = $timeoutSeconds;
    }

    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    public function getJavaBinary(): string
    {
        return $this->javaBinary;
    }

    public function getSaxonJarPath(): string
    {
        return $this->saxonJarPath;
    }

    public function getStylesheetsDir(): string
    {
        return $this->stylesheetsDir;
    }

    public function getTimeoutSeconds(): int
    {
        return $this->timeoutSeconds;
    }

    private function resolvePath(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            throw new InvalidArgumentException('Runtime path must not be empty.');
        }

        if ($this->isAbsolutePath($path)) {
            return $path;
        }

        return $this->projectDir . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    private function isAbsolutePath(string $path): bool
    {
        if ($path === '') {
            return false;
        }

        if ($path[0] === '/' || $path[0] === '\\') {
            return true;
        }

        return (bool) preg_match('/^[A-Za-z]:[\\\\\\/]/', $path);
    }
}

