<?php

declare(strict_types=1);

namespace App\Services\Epidoc\Xslt;

final class SaxonCommandBuilder
{
    private SaxonRuntimeConfig $config;

    public function __construct(SaxonRuntimeConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param array<string, string> $parameters
     * @return string[]
     */
    public function buildTransformCommand(string $sourceXmlPath, string $stylesheetPath, array $parameters = []): array
    {
        $command = $this->buildJavaInvocation();
        $command[] = '-s:' . $sourceXmlPath;
        $command[] = '-xsl:' . $this->resolveStylesheetPath($stylesheetPath);

        foreach ($parameters as $name => $value) {
            $command[] = $name . '=' . $value;
        }

        return $command;
    }

    /**
     * @return string[]
     */
    private function buildJavaInvocation(): array
    {
        $saxonJarPath = $this->config->getSaxonJarPath();
        $classpathEntries = [$saxonJarPath];

        $saxonDir = dirname($saxonJarPath);
        $libDir = $saxonDir . DIRECTORY_SEPARATOR . 'lib';
        if (is_dir($libDir)) {
            $libJars = glob($libDir . DIRECTORY_SEPARATOR . '*.jar');
            if (is_array($libJars)) {
                sort($libJars);
                foreach ($libJars as $libJar) {
                    if (is_string($libJar) && $libJar !== '') {
                        $classpathEntries[] = $libJar;
                    }
                }
            }
        }

        if (count($classpathEntries) > 1) {
            return [
                $this->config->getJavaBinary(),
                '-cp',
                implode(PATH_SEPARATOR, $classpathEntries),
                'net.sf.saxon.Transform',
            ];
        }

        return [
            $this->config->getJavaBinary(),
            '-jar',
            $saxonJarPath,
        ];
    }

    private function resolveStylesheetPath(string $stylesheetPath): string
    {
        if ($stylesheetPath === '') {
            return $stylesheetPath;
        }

        if ($this->isAbsolutePath($stylesheetPath)) {
            return $stylesheetPath;
        }

        return rtrim($this->config->getStylesheetsDir(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . ltrim($stylesheetPath, DIRECTORY_SEPARATOR);
    }

    private function isAbsolutePath(string $path): bool
    {
        if ($path === '') {
            return false;
        }

        if ($path[0] === '/' || $path[0] === '\\') {
            return true;
        }

        return (bool) preg_match('/^[A-Za-z]:[\\\\\\/]/', $path);
    }
}
