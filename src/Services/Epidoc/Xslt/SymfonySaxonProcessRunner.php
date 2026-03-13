<?php

declare(strict_types=1);

namespace App\Services\Epidoc\Xslt;

use Symfony\Component\Process\Process;

final class SymfonySaxonProcessRunner implements SaxonProcessRunnerInterface
{
    private SaxonRuntimeConfig $runtimeConfig;

    public function __construct(SaxonRuntimeConfig $runtimeConfig)
    {
        $this->runtimeConfig = $runtimeConfig;
    }

    public function run(array $command, ?string $workingDirectory = null): SaxonProcessRunResult
    {
        $process = new Process($command, $workingDirectory);
        $process->setTimeout($this->runtimeConfig->getTimeoutSeconds());
        $process->run();

        return new SaxonProcessRunResult(
            $process->getExitCode() ?? 1,
            $process->getOutput(),
            $process->getErrorOutput()
        );
    }
}

