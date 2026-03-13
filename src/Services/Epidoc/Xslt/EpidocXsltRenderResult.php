<?php

declare(strict_types=1);

namespace App\Services\Epidoc\Xslt;

final class EpidocXsltRenderResult
{
    private ?string $editionHtml;
    private ?string $apparatusHtml;
    private ?string $translationsHtml;
    /**
     * @var array<mixed>|null
     */
    private ?array $variantModel;
    /**
     * @var string[]
     */
    private array $errors;
    /**
     * @var string[]
     */
    private array $warnings;

    /**
     * @param array<mixed>|null $variantModel
     * @param string[] $errors
     * @param string[] $warnings
     */
    public function __construct(
        ?string $editionHtml = null,
        ?string $apparatusHtml = null,
        ?string $translationsHtml = null,
        ?array $variantModel = null,
        array $errors = [],
        array $warnings = []
    ) {
        $this->editionHtml = $editionHtml;
        $this->apparatusHtml = $apparatusHtml;
        $this->translationsHtml = $translationsHtml;
        $this->variantModel = $variantModel;
        $this->errors = array_values($errors);
        $this->warnings = array_values($warnings);
    }

    public function getEditionHtml(): ?string
    {
        return $this->editionHtml;
    }

    public function getApparatusHtml(): ?string
    {
        return $this->apparatusHtml;
    }

    public function getTranslationsHtml(): ?string
    {
        return $this->translationsHtml;
    }

    /**
     * @return array<mixed>|null
     */
    public function getVariantModel(): ?array
    {
        return $this->variantModel;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return string[]
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }
}

