<?php

declare(strict_types=1);

/*
 * This file is part of «Epigraphy of Medieval Rus» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus» database is distributed
 * in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\Services\Media\Thumbnails;

use App\Persistence\Entity\Media\File;
use Imagick;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class ThumbnailsGenerator implements ThumbnailsGeneratorInterface
{
    private HttpClientInterface $httpClient;

    private string $thumbnailsDirectory;

    private array $thumbnailPresets;

    private LoggerInterface $logger;

    public function __construct(
        HttpClientInterface $httpClient,
        string $thumbnailsDirectory,
        array $thumbnailPresets,
        LoggerInterface $logger
    ) {
        $this->httpClient = $httpClient;
        $this->thumbnailsDirectory = $thumbnailsDirectory;
        $this->thumbnailPresets = $thumbnailPresets;
        $this->logger = $logger;
    }

    public function getThumbnail(File $file, string $presetKey = 'default'): string
    {
        $this->validatePresetKey($presetKey);

        $policy = $this->getPolicy($file, $presetKey);

        if (null !== $policy) {
            $thumbnailFileName = $this->getThumbnailFileName($file, $presetKey, $policy);

            $pathToThumbnail = $this->getPathToThumbnail($thumbnailFileName);

            if (is_file($pathToThumbnail)) {
                return $this->getUrlOfThumbnail($thumbnailFileName);
            }

            $this->logger->warning(
                sprintf(
                    'Thumbnail for file "%s" and preset key "%s" does not exit (expected thumbnail file: "%s")',
                    $file->getFileName(),
                    $presetKey,
                    $pathToThumbnail
                )
            );
        } else {
            $this->logger->warning(
                sprintf(
                    'No policy found to handle thumbnails for file "%s" and preset key "%s"',
                    $file->getFileName(),
                    $presetKey
                )
            );
        }

        return $file->getUrl();
    }

    public function generateAll(File $file): void
    {
        $this
            ->logger
            ->info(sprintf('[ThumbnailsGenerator] <generateAll> $file->getFileName() = "%s"', $file->getFileName()));

        foreach ($this->thumbnailPresets as $presetKey => $preset) {
            $this->generateThumbnail($file, $presetKey);
        }
    }

    private function generateThumbnail(File $file, string $presetKey = 'default'): void
    {
        $this->validatePresetKey($presetKey);

        $policy = $this->getPolicy($file, $presetKey);

        if (null === $policy) {
            return;
        }

        $thumbnailFileName = $this->getThumbnailFileName($file, $presetKey, $policy);

        $pathToThumbnail = $this->getPathToThumbnail($thumbnailFileName);

        if (!is_file($pathToThumbnail)) {
            try {
                $startTime = microtime(true);

                switch ($policy['type']) {
                    case 'jpeg':
                        $this->handleJpeg($file, $pathToThumbnail, $policy);
                        break;

                    case 'raw':
                        $this->handleRaw($file, $pathToThumbnail);
                        break;

                    default:
                        throw $this->unknownPolicyException($policy['type']);
                }

                $endTime = microtime(true);

                $this->logger->info(
                    sprintf(
                        'Generated thumbnail for file "%s" and preset "%s" in %d seconds',
                        $file->getFileName(),
                        $presetKey,
                        $endTime - $startTime
                    )
                );
            } catch (Throwable $throwable) {
                $this->logger->error(
                    sprintf(
                        'Error when generating thumbnail for file "%s"',
                        $file->getFileName()
                    ),
                    ['exception' => $throwable]
                );
            }

            if (!is_file($pathToThumbnail)) {
                $this->logger->error(sprintf('Thumbnail for file "%s" has not been generated', $file->getFileName()));
            }
        }
    }

    private function validatePresetKey(string $presetKey): void
    {
        if (!\array_key_exists($presetKey, $this->thumbnailPresets)) {
            throw new RuntimeException(
                sprintf(
                    'Unknown thumbnail preset key "%s". Known presets are: %s',
                    $presetKey,
                    implode(', ', array_map(fn ($key) => sprintf('"%s"', $key), array_keys($this->thumbnailPresets)))
                )
            );
        }
    }

    private function ensureDirectoryExists(): void
    {
        if (!file_exists($this->thumbnailsDirectory)) {
            mkdir($this->thumbnailsDirectory, 0755, true);
        }
    }

    private function getPolicy(File $file, string $presetKey): ?array
    {
        $preset = $this->thumbnailPresets[$presetKey];

        foreach ($preset as $policy) {
            if (\in_array($file->getMediaType(), $policy['media-types'], true)) {
                return $policy;
            }
        }

        return null;
    }

    private function handleJpeg(File $file, string $pathToThumbnail, array $policy): void
    {
        try {
            $this->ensureDirectoryExists();

            $imagick = new Imagick($file->getUrl());

            if ($imagick->getImageWidth() > $imagick->getImageHeight()) {
                $thumbnailWidth = $policy['max-dimension'];
                $thumbnailHeight = (int) ($thumbnailWidth / $imagick->getImageWidth() * $imagick->getImageHeight());
            } else {
                $thumbnailHeight = $policy['max-dimension'];
                $thumbnailWidth = (int) ($thumbnailHeight / $imagick->getImageHeight() * $imagick->getImageWidth());
            }

            $imagick->setImageFormat($policy['extension']);
            $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
            $imagick->setImageCompressionQuality($policy['quality']);
            $imagick->thumbnailImage($thumbnailWidth, $thumbnailHeight);

            $imagick->writeImage($pathToThumbnail);
        } finally {
            if (isset($imagick)) {
                $imagick->clear();
                $imagick->destroy();
            }
        }
    }

    private function handleRaw(File $file, string $pathToThumbnail): void
    {
        $response = $this->httpClient->request('GET', $file->getUrl());

        file_put_contents($pathToThumbnail, $response->toStream());
    }

    private function unknownPolicyException(string $policyType): RuntimeException
    {
        return new RuntimeException(sprintf('Unknown policy type "%s".', $policyType));
    }

    private function getThumbnailFileName(File $file, string $presetKey, array $policy): string
    {
        switch ($policy['type']) {
            case 'jpeg':
                return $file->getFileName().'_thumb-'.$presetKey.'.'.$policy['extension'];
            case 'raw':
                return $file->getFileName();
            default:
                throw $this->unknownPolicyException($policy['type']);
        }
    }

    private function getUrlOfThumbnail(string $thumbnailFileName): string
    {
        return '/thumbs/'.$thumbnailFileName;
    }

    private function getPathToThumbnail(string $thumbnailFileName): string
    {
        return $this->thumbnailsDirectory.\DIRECTORY_SEPARATOR.$thumbnailFileName;
    }
}
