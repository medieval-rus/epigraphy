<?php

declare(strict_types=1);

namespace App\Tests\Xslt;

use App\Services\Epidoc\Xslt\SaxonCommandBuilder;
use App\Services\Epidoc\Xslt\SaxonRuntimeConfig;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SaxonRuntimeConfigTest extends TestCase
{
    public function testConfigNormalizesRelativePathsAndBuildsCommand(): void
    {
        $config = new SaxonRuntimeConfig(
            '/tmp/project',
            'java',
            'tools/saxon/saxon-he.jar',
            'tools/epidoc-stylesheets',
            10
        );

        self::assertSame('/tmp/project/tools/saxon/saxon-he.jar', $config->getSaxonJarPath());
        self::assertSame('/tmp/project/tools/epidoc-stylesheets', $config->getStylesheetsDir());
        self::assertSame(10, $config->getTimeoutSeconds());

        $builder = new SaxonCommandBuilder($config);
        $command = $builder->buildTransformCommand('/tmp/source.xml', 'start-edition.xsl', [
            'outputStyle' => 'html',
        ]);

        self::assertSame(
            [
                'java',
                '-jar',
                '/tmp/project/tools/saxon/saxon-he.jar',
                '-s:/tmp/source.xml',
                '-xsl:/tmp/project/tools/epidoc-stylesheets/start-edition.xsl',
                'outputStyle=html',
            ],
            $command
        );
    }

    public function testBuildsClasspathCommandWhenSaxonLibJarsExist(): void
    {
        $projectDir = sys_get_temp_dir() . '/saxon_builder_test_' . uniqid('', true);
        mkdir($projectDir . '/tools/saxon/lib', 0777, true);
        mkdir($projectDir . '/tools/epidoc-stylesheets', 0777, true);
        file_put_contents($projectDir . '/tools/saxon/saxon-he.jar', 'fake');
        file_put_contents($projectDir . '/tools/saxon/lib/xmlresolver-6.0.21.jar', 'fake');
        file_put_contents($projectDir . '/tools/epidoc-stylesheets/start-edition.xsl', '<xsl:stylesheet version="1.0"/>');

        $config = new SaxonRuntimeConfig(
            $projectDir,
            'java',
            'tools/saxon/saxon-he.jar',
            'tools/epidoc-stylesheets',
            10
        );

        $builder = new SaxonCommandBuilder($config);
        $command = $builder->buildTransformCommand('/tmp/source.xml', 'start-edition.xsl');

        self::assertSame('java', $command[0]);
        self::assertSame('-cp', $command[1]);
        self::assertStringContainsString($projectDir . '/tools/saxon/saxon-he.jar', $command[2]);
        self::assertStringContainsString($projectDir . '/tools/saxon/lib/xmlresolver-6.0.21.jar', $command[2]);
        self::assertSame('net.sf.saxon.Transform', $command[3]);
        self::assertSame('-s:/tmp/source.xml', $command[4]);
        self::assertSame('-xsl:' . $projectDir . '/tools/epidoc-stylesheets/start-edition.xsl', $command[5]);

        $this->removeDirectory($projectDir);
    }

    public function testInvalidTimeoutIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('positive integer');

        new SaxonRuntimeConfig(
            '/tmp/project',
            'java',
            'tools/saxon/saxon-he.jar',
            'tools/epidoc-stylesheets',
            0
        );
    }

    private function removeDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $items = scandir($path);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $itemPath = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($itemPath)) {
                $this->removeDirectory($itemPath);
            } else {
                @unlink($itemPath);
            }
        }

        @rmdir($path);
    }
}
