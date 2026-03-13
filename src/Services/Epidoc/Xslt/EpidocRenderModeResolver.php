<?php

declare(strict_types=1);

namespace App\Services\Epidoc\Xslt;

final class EpidocRenderModeResolver
{
    public const MODE_LEGACY_JS = 'legacy_js';
    public const MODE_XSLT_MVP = 'xslt_mvp';
    public const MODE_HYBRID = 'hybrid';

    private string $configuredMode;

    /**
     * @var string[]
     */
    private array $warnings = [];

    public function __construct(string $configuredMode)
    {
        $normalized = trim(strtolower($configuredMode));
        if (!in_array($normalized, self::allModes(), true)) {
            $this->configuredMode = self::MODE_LEGACY_JS;
            $this->warnings[] = sprintf(
                'Unknown EpiDoc render mode "%s"; falling back to "%s".',
                $configuredMode,
                self::MODE_LEGACY_JS
            );

            return;
        }

        $this->configuredMode = $normalized;
    }

    public static function allModes(): array
    {
        return [
            self::MODE_LEGACY_JS,
            self::MODE_XSLT_MVP,
            self::MODE_HYBRID,
        ];
    }

    public function getConfiguredMode(): string
    {
        return $this->configuredMode;
    }

    /**
     * @return string[]
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function shouldAttemptXslt(): bool
    {
        return $this->configuredMode === self::MODE_XSLT_MVP
            || $this->configuredMode === self::MODE_HYBRID;
    }

    public function shouldUseLegacyJsByDefault(): bool
    {
        return $this->configuredMode === self::MODE_LEGACY_JS;
    }

    public function shouldFallbackToLegacyOnXsltFailure(): bool
    {
        // Stage 4 safety rule: never fail the page due to XSLT runtime issues.
        return true;
    }

    public function shouldUseHybridUiComposition(): bool
    {
        return $this->configuredMode === self::MODE_HYBRID;
    }
}
