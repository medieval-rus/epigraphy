<?php

declare(strict_types=1);

namespace App\Tests\Xslt;

use App\Services\Epidoc\Xslt\EpidocRenderModeResolver;
use PHPUnit\Framework\TestCase;

final class EpidocRenderModeResolverTest extends TestCase
{
    public function testSupportedModesProduceExpectedPolicyMatrix(): void
    {
        $legacy = new EpidocRenderModeResolver('legacy_js');
        self::assertSame('legacy_js', $legacy->getConfiguredMode());
        self::assertTrue($legacy->shouldUseLegacyJsByDefault());
        self::assertFalse($legacy->shouldAttemptXslt());
        self::assertFalse($legacy->shouldUseHybridUiComposition());
        self::assertTrue($legacy->shouldFallbackToLegacyOnXsltFailure());

        $xslt = new EpidocRenderModeResolver('xslt_mvp');
        self::assertSame('xslt_mvp', $xslt->getConfiguredMode());
        self::assertFalse($xslt->shouldUseLegacyJsByDefault());
        self::assertTrue($xslt->shouldAttemptXslt());
        self::assertFalse($xslt->shouldUseHybridUiComposition());
        self::assertTrue($xslt->shouldFallbackToLegacyOnXsltFailure());

        $hybrid = new EpidocRenderModeResolver('hybrid');
        self::assertSame('hybrid', $hybrid->getConfiguredMode());
        self::assertFalse($hybrid->shouldUseLegacyJsByDefault());
        self::assertTrue($hybrid->shouldAttemptXslt());
        self::assertTrue($hybrid->shouldUseHybridUiComposition());
        self::assertTrue($hybrid->shouldFallbackToLegacyOnXsltFailure());
    }

    public function testUnknownModeFallsBackToLegacyAndProducesWarning(): void
    {
        $resolver = new EpidocRenderModeResolver('weird_mode');

        self::assertSame(EpidocRenderModeResolver::MODE_LEGACY_JS, $resolver->getConfiguredMode());
        self::assertTrue($resolver->shouldUseLegacyJsByDefault());
        self::assertFalse($resolver->shouldAttemptXslt());
        self::assertNotEmpty($resolver->getWarnings());
        self::assertStringContainsString('falling back', $resolver->getWarnings()[0]);
    }
}

