<?php

declare(strict_types=1);

namespace App\Tests\Xslt;

use App\Services\Epidoc\Xslt\EpidocEditionHtmlPostProcessor;
use PHPUnit\Framework\TestCase;

final class EpidocEditionHtmlPostProcessorTest extends TestCase
{
    public function testAddsSemanticHooksForEditionAppAndReadings(): void
    {
        $processor = new EpidocEditionHtmlPostProcessor();

        $html = '<div class="edition"><span class="app"><span class="lem">A</span><span class="rdg">B</span></span></div>';
        $result = $processor->addSemanticHooks($html);

        self::assertStringContainsString('data-epidoc-role="edition-root"', $result);
        self::assertStringContainsString('data-epidoc-hook="variant-app"', $result);
        self::assertStringContainsString('data-app-id="app-1"', $result);
        self::assertStringContainsString('data-reading-kind="lem"', $result);
        self::assertStringContainsString('data-reading-kind="rdg"', $result);
    }

    public function testAddsSemanticHooksForProjectSpecificClasses(): void
    {
        $processor = new EpidocEditionHtmlPostProcessor();

        $html = '<div class="epidoc-edition-text"><span class="epidoc-supplied">x</span><span class="epidoc-gap">***</span><span class="epidoc-unclear">y</span></div>';
        $result = $processor->addSemanticHooks($html);

        self::assertStringContainsString('data-epidoc-hook="edition-root"', $result);
        self::assertStringContainsString('data-epidoc-hook="supplied"', $result);
        self::assertStringContainsString('data-supplied-reason="lost"', $result);
        self::assertStringContainsString('data-epidoc-hook="gap"', $result);
        self::assertStringContainsString('data-epidoc-hook="unclear"', $result);
    }

    public function testPreservesUtf8Text(): void
    {
        $processor = new EpidocEditionHtmlPostProcessor();

        $html = '<div class="edition">Господи, помоги рабу своему</div>';
        $result = $processor->addSemanticHooks($html);

        self::assertStringContainsString('Господи, помоги рабу своему', $result);
        self::assertStringNotContainsString('ÐÐ¾Ñ', $result);
    }

    public function testRecognizesEditionIdAndUnderlineAsSuppliedHook(): void
    {
        $processor = new EpidocEditionHtmlPostProcessor();

        $html = '<div id="edition"><span class="underline">ивьнꙑ</span></div>';
        $result = $processor->addSemanticHooks($html);

        self::assertStringContainsString('id="edition"', $result);
        self::assertStringContainsString('data-epidoc-role="edition-root"', $result);
        self::assertStringContainsString('class="underline"', $result);
        self::assertStringContainsString('data-epidoc-hook="supplied"', $result);
    }

    public function testExtractsSuppliedReasonFromUnclearClass(): void
    {
        $processor = new EpidocEditionHtmlPostProcessor();

        $html = '<div id="edition"><span class="epidoc-supplied epidoc-supplied--unclear">и̣в̣</span></div>';
        $result = $processor->addSemanticHooks($html);

        self::assertStringContainsString('data-supplied-reason="unclear"', $result);
    }
}
