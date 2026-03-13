<?php

declare(strict_types=1);

namespace App\Services\Epidoc\Xslt;

interface SaxonProcessRunnerInterface
{
    /**
     * @param string[] $command
     */
    public function run(array $command, ?string $workingDirectory = null): SaxonProcessRunResult;
}

