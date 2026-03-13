<?php

declare(strict_types=1);

namespace App\Tests\Xslt;

use App\Services\Epidoc\Xslt\EpidocXsltRenderer;
use App\Services\Epidoc\Xslt\EpidocEditionHtmlPostProcessor;
use App\Services\Epidoc\Xslt\SaxonCommandBuilder;
use App\Services\Epidoc\Xslt\SaxonProcessRunResult;
use App\Services\Epidoc\Xslt\SaxonProcessRunnerInterface;
use App\Services\Epidoc\Xslt\SaxonRuntimeConfig;
use PHPUnit\Framework\TestCase;

final class EpidocXsltRendererTest extends TestCase
{
    public function testRenderReturnsEditionHtmlOnSuccessfulSaxonRun(): void
    {
        $projectDir = sys_get_temp_dir() . '/epidoc_xslt_renderer_test_' . uniqid('', true);
        mkdir($projectDir . '/tools/epidoc-stylesheets', 0777, true);
        mkdir($projectDir . '/tools/xslt', 0777, true);
        mkdir($projectDir . '/tools/saxon', 0777, true);
        file_put_contents($projectDir . '/tools/epidoc-stylesheets/start-edition.xsl', '<xsl:stylesheet version="1.0"/>');
        file_put_contents($projectDir . '/tools/xslt/start-edition-epigraphy.xsl', '<xsl:stylesheet version="1.0"/>');
        file_put_contents($projectDir . '/tools/saxon/saxon-he.jar', 'fake');

        $runtime = new SaxonRuntimeConfig(
            $projectDir,
            'java',
            'tools/saxon/saxon-he.jar',
            'tools/epidoc-stylesheets',
            10
        );

        $fakeRunner = new class implements SaxonProcessRunnerInterface {
            /**
             * @var string[]|null
             */
            public ?array $lastCommand = null;
            public ?string $lastWorkingDirectory = null;

            public function run(array $command, ?string $workingDirectory = null): SaxonProcessRunResult
            {
                $this->lastCommand = $command;
                $this->lastWorkingDirectory = $workingDirectory;

                return new SaxonProcessRunResult(0, '<div class="edition">OK</div>', '');
            }
        };

        $renderer = new EpidocXsltRenderer(
            $runtime,
            new SaxonCommandBuilder($runtime),
            $fakeRunner,
            new EpidocEditionHtmlPostProcessor()
        );

        $result = $renderer->render('<TEI xmlns="http://www.tei-c.org/ns/1.0"><text><body/></text></TEI>');

        self::assertFalse($result->hasErrors());
        self::assertNotNull($result->getEditionHtml());
        self::assertStringContainsString('<div class="edition"', (string) $result->getEditionHtml());
        self::assertStringContainsString('data-epidoc-role="edition-root"', (string) $result->getEditionHtml());
        self::assertStringContainsString('>OK</div>', (string) $result->getEditionHtml());
        self::assertSame($projectDir, $fakeRunner->lastWorkingDirectory);
        self::assertNotNull($fakeRunner->lastCommand);
        self::assertContains('-jar', $fakeRunner->lastCommand);

        $joinedCommand = implode(' ', $fakeRunner->lastCommand);
        self::assertStringContainsString('start-edition-epigraphy.xsl', $joinedCommand);
        self::assertStringContainsString('-s:', $joinedCommand);

        $this->removeDirectory($projectDir);
    }

    public function testRenderExtractsBodyContentWhenXsltReturnsFullHtmlDocument(): void
    {
        $projectDir = sys_get_temp_dir() . '/epidoc_xslt_renderer_test_' . uniqid('', true);
        mkdir($projectDir . '/tools/epidoc-stylesheets', 0777, true);
        mkdir($projectDir . '/tools/xslt', 0777, true);
        mkdir($projectDir . '/tools/saxon', 0777, true);
        file_put_contents($projectDir . '/tools/epidoc-stylesheets/start-edition.xsl', '<xsl:stylesheet version="1.0"/>');
        file_put_contents($projectDir . '/tools/xslt/start-edition-epigraphy.xsl', '<xsl:stylesheet version="1.0"/>');
        file_put_contents($projectDir . '/tools/saxon/saxon-he.jar', 'fake');

        $runtime = new SaxonRuntimeConfig(
            $projectDir,
            'java',
            'tools/saxon/saxon-he.jar',
            'tools/epidoc-stylesheets',
            10
        );

        $fakeRunner = new class implements SaxonProcessRunnerInterface {
            public function run(array $command, ?string $workingDirectory = null): SaxonProcessRunResult
            {
                return new SaxonProcessRunResult(
                    0,
                    "<html><head><title>x</title></head><body>\n<div class=\"edition\">Body only</div>\n</body></html>",
                    ''
                );
            }
        };

        $renderer = new EpidocXsltRenderer(
            $runtime,
            new SaxonCommandBuilder($runtime),
            $fakeRunner,
            new EpidocEditionHtmlPostProcessor()
        );

        $result = $renderer->render('<TEI xmlns="http://www.tei-c.org/ns/1.0"><text><body/></text></TEI>');

        self::assertFalse($result->hasErrors());
        self::assertNotNull($result->getEditionHtml());
        self::assertStringContainsString('<div class="edition"', (string) $result->getEditionHtml());
        self::assertStringContainsString('data-epidoc-role="edition-root"', (string) $result->getEditionHtml());
        self::assertStringContainsString('Body only', (string) $result->getEditionHtml());

        $this->removeDirectory($projectDir);
    }

    public function testRenderPrefersEditionFragmentAndExcludesTranslationsFromFullHtml(): void
    {
        $projectDir = sys_get_temp_dir() . '/epidoc_xslt_renderer_test_' . uniqid('', true);
        mkdir($projectDir . '/tools/epidoc-stylesheets', 0777, true);
        mkdir($projectDir . '/tools/xslt', 0777, true);
        mkdir($projectDir . '/tools/saxon', 0777, true);
        file_put_contents($projectDir . '/tools/epidoc-stylesheets/start-edition.xsl', '<xsl:stylesheet version="1.0"/>');
        file_put_contents($projectDir . '/tools/xslt/start-edition-epigraphy.xsl', '<xsl:stylesheet version="1.0"/>');
        file_put_contents($projectDir . '/tools/saxon/saxon-he.jar', 'fake');

        $runtime = new SaxonRuntimeConfig(
            $projectDir,
            'java',
            'tools/saxon/saxon-he.jar',
            'tools/epidoc-stylesheets',
            10
        );

        $fakeRunner = new class implements SaxonProcessRunnerInterface {
            public function run(array $command, ?string $workingDirectory = null): SaxonProcessRunResult
            {
                return new SaxonProcessRunResult(
                    0,
                    '<html><body>'
                    . '<div class="edition">Ги҃ помози</div>'
                    . '<div class="translation">Господи, помоги</div>'
                    . '</body></html>',
                    ''
                );
            }
        };

        $renderer = new EpidocXsltRenderer(
            $runtime,
            new SaxonCommandBuilder($runtime),
            $fakeRunner,
            new EpidocEditionHtmlPostProcessor()
        );

        $result = $renderer->render('<TEI xmlns="http://www.tei-c.org/ns/1.0"><text><body/></text></TEI>');

        self::assertFalse($result->hasErrors());
        self::assertNotNull($result->getEditionHtml());
        self::assertStringContainsString('Ги҃ помози', (string) $result->getEditionHtml());
        self::assertStringNotContainsString('translation', (string) $result->getEditionHtml());
        self::assertStringNotContainsString('Господи, помоги', (string) $result->getEditionHtml());

        $this->removeDirectory($projectDir);
    }

    public function testRenderRecognizesEditionByIdAttribute(): void
    {
        $projectDir = sys_get_temp_dir() . '/epidoc_xslt_renderer_test_' . uniqid('', true);
        mkdir($projectDir . '/tools/epidoc-stylesheets', 0777, true);
        mkdir($projectDir . '/tools/xslt', 0777, true);
        mkdir($projectDir . '/tools/saxon', 0777, true);
        file_put_contents($projectDir . '/tools/epidoc-stylesheets/start-edition.xsl', '<xsl:stylesheet version="1.0"/>');
        file_put_contents($projectDir . '/tools/xslt/start-edition-epigraphy.xsl', '<xsl:stylesheet version="1.0"/>');
        file_put_contents($projectDir . '/tools/saxon/saxon-he.jar', 'fake');

        $runtime = new SaxonRuntimeConfig($projectDir, 'java', 'tools/saxon/saxon-he.jar', 'tools/epidoc-stylesheets', 10);

        $fakeRunner = new class implements SaxonProcessRunnerInterface {
            public function run(array $command, ?string $workingDirectory = null): SaxonProcessRunResult
            {
                return new SaxonProcessRunResult(
                    0,
                    '<html><body><div id="edition">EDITION</div><div class="translation">T</div></body></html>',
                    ''
                );
            }
        };

        $renderer = new EpidocXsltRenderer($runtime, new SaxonCommandBuilder($runtime), $fakeRunner, new EpidocEditionHtmlPostProcessor());
        $result = $renderer->render('<TEI xmlns="http://www.tei-c.org/ns/1.0"><text><body/></text></TEI>');

        self::assertFalse($result->hasErrors());
        self::assertStringContainsString('id="edition"', (string) $result->getEditionHtml());
        self::assertStringNotContainsString('class="translation"', (string) $result->getEditionHtml());

        $this->removeDirectory($projectDir);
    }

    public function testRenderFailsGracefullyWhenSaxonJarMissing(): void
    {
        $projectDir = sys_get_temp_dir() . '/epidoc_xslt_renderer_test_' . uniqid('', true);
        mkdir($projectDir . '/tools/epidoc-stylesheets', 0777, true);
        mkdir($projectDir . '/tools/xslt', 0777, true);
        file_put_contents($projectDir . '/tools/epidoc-stylesheets/start-edition.xsl', '<xsl:stylesheet version="1.0"/>');
        file_put_contents($projectDir . '/tools/xslt/start-edition-epigraphy.xsl', '<xsl:stylesheet version="1.0"/>');

        $runtime = new SaxonRuntimeConfig(
            $projectDir,
            'java',
            'tools/saxon/saxon-he.jar',
            'tools/epidoc-stylesheets',
            10
        );

        $fakeRunner = new class implements SaxonProcessRunnerInterface {
            public bool $called = false;

            public function run(array $command, ?string $workingDirectory = null): SaxonProcessRunResult
            {
                $this->called = true;

                return new SaxonProcessRunResult(0, '', '');
            }
        };

        $renderer = new EpidocXsltRenderer(
            $runtime,
            new SaxonCommandBuilder($runtime),
            $fakeRunner,
            new EpidocEditionHtmlPostProcessor()
        );

        $result = $renderer->render('<TEI xmlns="http://www.tei-c.org/ns/1.0"><text><body/></text></TEI>');

        self::assertTrue($result->hasErrors());
        self::assertNull($result->getEditionHtml());
        self::assertFalse($fakeRunner->called, 'Runner must not be called when runtime validation fails.');
        self::assertStringContainsString('Saxon jar not found', implode("\n", $result->getErrors()));

        $this->removeDirectory($projectDir);
    }

    public function testRenderFailsGracefullyWhenXsltOutputIsEmpty(): void
    {
        $projectDir = sys_get_temp_dir() . '/epidoc_xslt_renderer_test_' . uniqid('', true);
        mkdir($projectDir . '/tools/epidoc-stylesheets', 0777, true);
        mkdir($projectDir . '/tools/xslt', 0777, true);
        mkdir($projectDir . '/tools/saxon', 0777, true);
        file_put_contents($projectDir . '/tools/epidoc-stylesheets/start-edition.xsl', '<xsl:stylesheet version="1.0"/>');
        file_put_contents($projectDir . '/tools/xslt/start-edition-epigraphy.xsl', '<xsl:stylesheet version="1.0"/>');
        file_put_contents($projectDir . '/tools/saxon/saxon-he.jar', 'fake');

        $runtime = new SaxonRuntimeConfig(
            $projectDir,
            'java',
            'tools/saxon/saxon-he.jar',
            'tools/epidoc-stylesheets',
            10
        );

        $fakeRunner = new class implements SaxonProcessRunnerInterface {
            public function run(array $command, ?string $workingDirectory = null): SaxonProcessRunResult
            {
                return new SaxonProcessRunResult(0, " \n\t ", '');
            }
        };

        $renderer = new EpidocXsltRenderer(
            $runtime,
            new SaxonCommandBuilder($runtime),
            $fakeRunner,
            new EpidocEditionHtmlPostProcessor()
        );

        $result = $renderer->render('<TEI xmlns="http://www.tei-c.org/ns/1.0"><text><body/></text></TEI>');

        self::assertTrue($result->hasErrors());
        self::assertNull($result->getEditionHtml());
        self::assertStringContainsString('empty output', implode("\n", $result->getErrors()));

        $this->removeDirectory($projectDir);
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
                continue;
            }

            @unlink($itemPath);
        }

        @rmdir($path);
    }
}
