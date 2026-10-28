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
        $metadataPath = $this->projectRoot . '/tools/epidoc-stylesheets.upstream.json';
        self::assertFileExists($metadataPath);
        $metadata = json_decode((string) file_get_contents($metadataPath), true, 512, JSON_THROW_ON_ERROR);
        self::assertMatchesRegularExpression('/^[0-9a-f]{40}$/', $metadata['commit']);
        foreach ($metadata['entrypoints'] as $name => $hash) {
            self::assertSame($hash, hash_file('sha256', $this->projectRoot . '/tools/epidoc-stylesheets/' . $name));
        }
        if (file_exists($this->projectRoot . '/tools/epidoc-stylesheets/.git')) {
            $process = new \Symfony\Component\Process\Process([
                'git', '-C', $this->projectRoot . '/tools/epidoc-stylesheets', 'rev-parse', 'HEAD',
            ]);
            $process->mustRun();
            self::assertSame($metadata['commit'], trim($process->getOutput()));
        }
    }
}
