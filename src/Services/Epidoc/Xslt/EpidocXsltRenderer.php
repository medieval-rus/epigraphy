<?php

declare(strict_types=1);

namespace App\Services\Epidoc\Xslt;

use Throwable;

final class EpidocXsltRenderer
{
    private const EDITION_STYLESHEET = '../xslt/start-edition-epigraphy.xsl';

    private SaxonRuntimeConfig $runtimeConfig;
    private SaxonCommandBuilder $commandBuilder;
    private SaxonProcessRunnerInterface $processRunner;
    private EpidocEditionHtmlPostProcessor $editionHtmlPostProcessor;

    public function __construct(
        SaxonRuntimeConfig $runtimeConfig,
        SaxonCommandBuilder $commandBuilder,
        SaxonProcessRunnerInterface $processRunner,
        EpidocEditionHtmlPostProcessor $editionHtmlPostProcessor
    ) {
        $this->runtimeConfig = $runtimeConfig;
        $this->commandBuilder = $commandBuilder;
        $this->processRunner = $processRunner;
        $this->editionHtmlPostProcessor = $editionHtmlPostProcessor;
    }

    public function render(string $xml): EpidocXsltRenderResult
    {
        if (trim($xml) === '') {
            return new EpidocXsltRenderResult(null, null, null, null, [
                'EpiDoc XSLT render failed: XML input is empty.',
            ]);
        }

        $preflightErrors = $this->validateRuntime();
        if ($preflightErrors !== []) {
            return new EpidocXsltRenderResult(null, null, null, null, $preflightErrors);
        }

        $sourceXmlPath = tempnam(sys_get_temp_dir(), 'epidoc_xslt_');
        if ($sourceXmlPath === false) {
            return new EpidocXsltRenderResult(null, null, null, null, [
                'EpiDoc XSLT render failed: unable to create temporary XML file.',
            ]);
        }

        try {
            if (file_put_contents($sourceXmlPath, $xml) === false) {
                return new EpidocXsltRenderResult(null, null, null, null, [
                    'EpiDoc XSLT render failed: unable to write XML into temporary file.',
                ]);
            }

            $command = $this->commandBuilder->buildTransformCommand(
                $sourceXmlPath,
                self::EDITION_STYLESHEET
            );

            $processResult = $this->processRunner->run($command, $this->runtimeConfig->getProjectDir());
            if (!$processResult->isSuccessful()) {
                $error = trim($processResult->getStderr());
                if ($error === '') {
                    $error = sprintf('Saxon process exited with code %d.', $processResult->getExitCode());
                }

                return new EpidocXsltRenderResult(null, null, null, null, [
                    'EpiDoc XSLT render failed: ' . $error,
                ]);
            }

            $editionHtml = $this->normalizeEditionOutput($processResult->getStdout());
            if ($editionHtml === '') {
                return new EpidocXsltRenderResult(null, null, null, null, [
                    'EpiDoc XSLT render failed: empty output from XSLT process.',
                ]);
            }

            $editionHtml = trim($this->editionHtmlPostProcessor->addSemanticHooks($editionHtml));
            if ($editionHtml === '') {
                return new EpidocXsltRenderResult(null, null, null, null, [
                    'EpiDoc XSLT render failed: empty output after semantic post-processing.',
                ]);
            }

            return new EpidocXsltRenderResult($editionHtml);
        } catch (Throwable $e) {
            return new EpidocXsltRenderResult(null, null, null, null, [
                'EpiDoc XSLT render failed: ' . $e->getMessage(),
            ]);
        } finally {
            @unlink($sourceXmlPath);
        }
    }

    /**
     * @return string[]
     */
    private function validateRuntime(): array
    {
        $errors = [];

        if (!is_dir($this->runtimeConfig->getStylesheetsDir())) {
            $errors[] = sprintf(
                'EpiDoc stylesheets directory not found: %s',
                $this->runtimeConfig->getStylesheetsDir()
            );
        }

        $editionStylesheetPath = rtrim($this->runtimeConfig->getStylesheetsDir(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . self::EDITION_STYLESHEET;
        if (!is_file($editionStylesheetPath)) {
            $errors[] = sprintf(
                'EpiDoc edition stylesheet not found: %s',
                $editionStylesheetPath
            );
        }

        if (!is_file($this->runtimeConfig->getSaxonJarPath())) {
            $errors[] = sprintf(
                'Saxon jar not found: %s',
                $this->runtimeConfig->getSaxonJarPath()
            );
        }

        return $errors;
    }

    private function normalizeEditionOutput(string $stdout): string
    {
        $stdout = trim($stdout);
        if ($stdout === '') {
            return '';
        }

        // If XSLT returns a full HTML document, extract the body contents for embedding into the table cell.
        if (stripos($stdout, '<html') !== false && stripos($stdout, '<body') !== false) {
            $editionHtml = $this->extractEditionFragmentFromHtmlDocument($stdout);
            if ($editionHtml !== null && trim($editionHtml) !== '') {
                return trim($editionHtml);
            }

            $bodyHtml = $this->extractBodyInnerHtml($stdout);
            if ($bodyHtml !== null && trim($bodyHtml) !== '') {
                return trim($bodyHtml);
            }
        }

        return $stdout;
    }

    private function extractBodyInnerHtml(string $html): ?string
    {
        if (!class_exists(\DOMDocument::class)) {
            return $this->extractBodyInnerHtmlWithRegex($html);
        }

        $previous = libxml_use_internal_errors(true);
        try {
            $dom = new \DOMDocument();
            if (!$dom->loadHTML($this->prepareHtmlForUtf8Parsing($html), LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR)) {
                return $this->extractBodyInnerHtmlWithRegex($html);
            }

            $bodyElements = $dom->getElementsByTagName('body');
            if ($bodyElements->count() === 0) {
                return $this->extractBodyInnerHtmlWithRegex($html);
            }

            $body = $bodyElements->item(0);
            if ($body === null) {
                return null;
            }

            $result = '';
            foreach ($body->childNodes as $child) {
                $result .= $dom->saveHTML($child);
            }

            return $result;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private function extractEditionFragmentFromHtmlDocument(string $html): ?string
    {
        if (!class_exists(\DOMDocument::class)) {
            return null;
        }

        $previous = libxml_use_internal_errors(true);
        try {
            $dom = new \DOMDocument();
            if (!$dom->loadHTML($this->prepareHtmlForUtf8Parsing($html), LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR)) {
                return null;
            }

            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query(
                '//*[@id="edition" or ' .
                'contains(concat(" ", normalize-space(@class), " "), " edition ") or ' .
                'contains(concat(" ", normalize-space(@class), " "), " epidoc-edition-text ")]'
            );

            if ($nodes === false || $nodes->count() === 0) {
                return null;
            }

            $result = '';
            foreach ($nodes as $node) {
                if ($node instanceof \DOMNode) {
                    $result .= $dom->saveHTML($node);
                }
            }

            return trim($result) !== '' ? $result : null;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private function prepareHtmlForUtf8Parsing(string $html): string
    {
        return '<?xml encoding="UTF-8">' . $html;
    }

    private function extractBodyInnerHtmlWithRegex(string $html): ?string
    {
        if (!preg_match('/<body\b[^>]*>([\s\S]*?)<\/body>/i', $html, $matches)) {
            return null;
        }

        return $matches[1];
    }
}
