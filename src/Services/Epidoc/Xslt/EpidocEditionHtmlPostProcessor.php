<?php

declare(strict_types=1);

namespace App\Services\Epidoc\Xslt;

final class EpidocEditionHtmlPostProcessor
{
    public function addSemanticHooks(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return $html;
        }

        if (!class_exists(\DOMDocument::class)) {
            return $html;
        }

        $previous = libxml_use_internal_errors(true);
        try {
            $dom = new \DOMDocument();
            $wrappedHtml = '<!DOCTYPE html><html><body><div id="epidoc-hook-root">' . $html . '</div></body></html>';
            if (!$dom->loadHTML($this->prepareHtmlForUtf8Parsing($wrappedHtml), LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR)) {
                return $html;
            }

            $xpath = new \DOMXPath($dom);

            $this->markEditionRoot($xpath);
            $this->markVariantApps($xpath);
            $this->markSemanticTypes($xpath);

            $root = $xpath->query('//*[@id="epidoc-hook-root"]')->item(0);
            if ($root === null) {
                return $html;
            }

            $result = '';
            foreach ($root->childNodes as $child) {
                $result .= $dom->saveHTML($child);
            }

            return $result !== '' ? $result : $html;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private function prepareHtmlForUtf8Parsing(string $html): string
    {
        return '<?xml encoding="UTF-8">' . $html;
    }

    private function markEditionRoot(\DOMXPath $xpath): void
    {
        $nodes = $xpath->query(
            '//*[@id="edition" or ' .
            'contains(concat(" ", normalize-space(@class), " "), " edition ") or ' .
            'contains(concat(" ", normalize-space(@class), " "), " epidoc-edition-text ")]'
        );

        if ($nodes === false || $nodes->count() === 0) {
            return;
        }

        foreach ($nodes as $node) {
            if (!$node instanceof \DOMElement) {
                continue;
            }
            $this->setDataAttribute($node, 'data-epidoc-role', 'edition-root');
            $this->setDataAttribute($node, 'data-epidoc-hook', 'edition-root');
        }
    }

    private function markVariantApps(\DOMXPath $xpath): void
    {
        $apps = $xpath->query(
            '//*[contains(concat(" ", normalize-space(@class), " "), " app ") or ' .
            'contains(concat(" ", normalize-space(@class), " "), " epidoc-app ")]'
        );

        if ($apps === false) {
            return;
        }

        $index = 1;
        foreach ($apps as $app) {
            if (!$app instanceof \DOMElement) {
                continue;
            }

            $this->setDataAttribute($app, 'data-epidoc-role', 'app');
            $this->setDataAttribute($app, 'data-epidoc-hook', 'variant-app');
            if (!$app->hasAttribute('data-app-id')) {
                $app->setAttribute('data-app-id', 'app-' . $index);
            }

            $lemNodes = $xpath->query(
                './/*[contains(concat(" ", normalize-space(@class), " "), " lem ") or ' .
                'contains(concat(" ", normalize-space(@class), " "), " epidoc-lem ")]',
                $app
            );
            if ($lemNodes !== false) {
                foreach ($lemNodes as $lem) {
                    if ($lem instanceof \DOMElement) {
                        $this->setDataAttribute($lem, 'data-epidoc-role', 'reading');
                        $this->setDataAttribute($lem, 'data-reading-kind', 'lem');
                    }
                }
            }

            $rdgNodes = $xpath->query(
                './/*[contains(concat(" ", normalize-space(@class), " "), " rdg ") or ' .
                'contains(concat(" ", normalize-space(@class), " "), " epidoc-rdg ")]',
                $app
            );
            if ($rdgNodes !== false) {
                foreach ($rdgNodes as $rdg) {
                    if ($rdg instanceof \DOMElement) {
                        $this->setDataAttribute($rdg, 'data-epidoc-role', 'reading');
                        $this->setDataAttribute($rdg, 'data-reading-kind', 'rdg');
                    }
                }
            }

            $index++;
        }
    }

    private function markSemanticTypes(\DOMXPath $xpath): void
    {
        $mappings = [
            'supplied' => [
                '//*[contains(concat(" ", normalize-space(@class), " "), " supplied ") or ' .
                'contains(concat(" ", normalize-space(@class), " "), " underline ") or ' .
                'contains(concat(" ", normalize-space(@class), " "), " epidoc-supplied ")]',
                'supplied',
            ],
            'unclear' => [
                '//*[contains(concat(" ", normalize-space(@class), " "), " unclear ") or ' .
                'contains(concat(" ", normalize-space(@class), " "), " epidoc-unclear ")]',
                'unclear',
            ],
            'gap' => [
                '//*[contains(concat(" ", normalize-space(@class), " "), " gap ") or ' .
                'contains(concat(" ", normalize-space(@class), " "), " epidoc-gap ")]',
                'gap',
            ],
        ];

        foreach ($mappings as $hook => [$query, $role]) {
            $nodes = $xpath->query($query);
            if ($nodes === false) {
                continue;
            }
            foreach ($nodes as $node) {
                if (!$node instanceof \DOMElement) {
                    continue;
                }
                $this->setDataAttribute($node, 'data-epidoc-role', $role);
                $this->setDataAttribute($node, 'data-epidoc-hook', $hook);
                if ($hook === 'supplied' && !$node->hasAttribute('data-supplied-reason')) {
                    $class = ' ' . $node->getAttribute('class') . ' ';
                    $reason = 'lost';
                    if (strpos($class, ' epidoc-supplied--editorial ') !== false) {
                        $reason = 'editorial';
                    } elseif (strpos($class, ' epidoc-supplied--unclear ') !== false) {
                        $reason = 'unclear';
                    }
                    $node->setAttribute(
                        'data-supplied-reason',
                        $reason
                    );
                }
            }
        }
    }

    private function setDataAttribute(\DOMElement $element, string $name, string $value): void
    {
        if (!$element->hasAttribute($name)) {
            $element->setAttribute($name, $value);
        }
    }
}
