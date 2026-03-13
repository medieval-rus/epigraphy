<?php

declare(strict_types=1);

namespace App\Tests\Xslt;

use PHPUnit\Framework\TestCase;

final class EpidocStylesheetsStageOneTest extends TestCase
{
    private string $projectRoot;

    protected function setUp(): void
    {
        $this->projectRoot = dirname(__DIR__, 2);
    }

    public function testSubmoduleDirectoryAndKeyEntrypointsExist(): void
    {
        $stylesheetsDir = $this->projectRoot . '/tools/epidoc-stylesheets';

        self::assertDirectoryExists($stylesheetsDir);
        self::assertFileExists($stylesheetsDir . '/start-edition.xsl');
        self::assertFileExists($stylesheetsDir . '/start-txt.xsl');
        self::assertFileExists($stylesheetsDir . '/LICENSE.txt');
    }

    public function testUpstreamMetadataMatchesCurrentSubmoduleRevision(): void
    {
        $metadataPath = $this->projectRoot . '/tools/epidoc-stylesheets.upstream.md';
        $headPath = $this->projectRoot . '/tools/epidoc-stylesheets/.git';

        self::assertFileExists($metadataPath);
        self::assertFileExists($headPath);

        $metadata = file_get_contents($metadataPath);
        self::assertIsString($metadata);

        $gitHeadPointer = trim((string) file_get_contents($headPath));
        self::assertStringStartsWith('gitdir: ', $gitHeadPointer);

        $gitDirPath = trim(substr($gitHeadPointer, strlen('gitdir: ')));
        if (!str_starts_with($gitDirPath, '/')) {
            $gitDirPath = realpath($this->projectRoot . '/tools/epidoc-stylesheets/' . $gitDirPath) ?: '';
        }

        self::assertNotSame('', $gitDirPath, 'Failed to resolve submodule gitdir path.');

        $headRef = trim((string) file_get_contents($gitDirPath . '/HEAD'));
        self::assertStringStartsWith('ref: ', $headRef);

        $refPath = $gitDirPath . '/' . trim(substr($headRef, strlen('ref: ')));
        self::assertFileExists($refPath);

        $currentCommit = trim((string) file_get_contents($refPath));
        self::assertMatchesRegularExpression('/^[0-9a-f]{40}$/', $currentCommit);

        self::assertStringContainsString($currentCommit, $metadata);
    }
}

