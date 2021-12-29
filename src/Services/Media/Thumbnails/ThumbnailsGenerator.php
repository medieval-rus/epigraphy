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

use App\Helper\StringHelper;
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

    private array $thumbnailsPolicies;

    private LoggerInterface $logger;

    public function __construct(
        HttpClientInterface $httpClient,
        string $thumbnailsDirectory,
        array $thumbnailsPolicies,
        LoggerInterface $logger
    ) {
        $this->httpClient = $httpClient;
        $this->thumbnailsDirectory = $thumbnailsDirectory;
        $this->thumbnailsPolicies = $thumbnailsPolicies;
        $this->logger = $logger;
    }

    public function getThumbnail(File $file, string $presetKey = 'default'): string
    {
        $policy = $this->getPolicy($file);

        if (null !== $policy) {
            $presets = $policy['presets'];

            if (!\array_key_exists($presetKey, $presets)) {
                throw new RuntimeException(
                    sprintf(
                        'Cannot get thumbnail for file "%s": unknown thumbnail preset key "%s". Known presets are: %s',
                        $file->getFileName(),
                        $presetKey,
                        implode(', ', array_map(fn ($key) => sprintf('"%s"', $key), array_keys($presets)))
                    )
                );
            }

            $thumbnailFileName = $this->getThumbnailFileName($file, $presetKey, $presets[$presetKey]);

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
            $this->logger->error(
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

        $policy = $this->getPolicy($file);

        if (null === $policy) {
            $this
                ->logger
                ->error(
                    sprintf(
                        '[ThumbnailsGenerator] <generateAll> Cannot find policy for file "%s"',
                        $file->getFileName()
                    )
                );

            return;
        }

        foreach ($policy['presets'] as $presetKey => $preset) {
            $this->generateThumbnail($file, $presetKey, $preset);
        }
    }

    public function regenerateAll(File $file): void
    {
        $this
            ->logger
            ->info(sprintf('[ThumbnailsGenerator] <regenerateAll> $file->getFileName() = "%s"', $file->getFileName()));

        $this->ensureDirectoryExists();

        $existingThumbnails = array_filter(
            scandir($this->thumbnailsDirectory),
            fn (string $item): bool => StringHelper::startsWith($item, $file->getFileName())
        );

        foreach ($existingThumbnails as $existingThumbnail) {
            unlink($this->getPathToThumbnail($existingThumbnail));
        }

        $this->generateAll($file);
    }

    private function generateThumbnail(File $file, string $presetKey, array $preset): void
    {
        $thumbnailFileName = $this->getThumbnailFileName($file, $presetKey, $preset);

        $pathToThumbnail = $this->getPathToThumbnail($thumbnailFileName);

        if (!is_file($pathToThumbnail)) {
            try {
                $startTime = microtime(true);

                switch ($preset['type']) {
                    case 'jpeg':
                        $this->handleJpeg($file, $pathToThumbnail, $preset);
                        break;
                    case 'raw':
                        $this->handleRaw($file, $pathToThumbnail);
                        break;
                    default:
                        throw $this->unknownPresetTypeException($preset['type']);
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

    private function ensureDirectoryExists(): void
    {
        if (!file_exists($this->thumbnailsDirectory)) {
            mkdir($this->thumbnailsDirectory, 0755, true);
        }
    }

    private function handleJpeg(File $file, string $pathToThumbnail, array $preset): void
    {
        try {
            $this->ensureDirectoryExists();

            $imagick = new Imagick($file->getUrl());

            if ($imagick->getImageWidth() > $imagick->getImageHeight()) {
                $thumbnailWidth = $preset['max-dimension'];
                $thumbnailHeight = (int) ($thumbnailWidth / $imagick->getImageWidth() * $imagick->getImageHeight());
            } else {
                $thumbnailHeight = $preset['max-dimension'];
                $thumbnailWidth = (int) ($thumbnailHeight / $imagick->getImageHeight() * $imagick->getImageWidth());
            }

            $imagick->setImageFormat($preset['extension']);
            $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
            $imagick->setImageCompressionQuality($preset['quality']);
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

    private function getThumbnailFileName(File $file, string $presetKey, array $preset): string
    {
        switch ($preset['type']) {
            case 'jpeg':
                return $file->getFileName().'_thumb-'.$presetKey.'.'.$preset['extension'];
            case 'raw':
                return $file->getFileName();
            default:
                throw $this->unknownPresetTypeException($preset['type']);
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

    private function getPolicy(File $file): ?array
    {
        foreach ($this->thumbnailsPolicies as $policy) {
            if (\in_array($file->getMediaType(), $policy['media-types'], true)) {
                return $policy;
            }
        }

        return null;
    }

    private function unknownPresetTypeException(string $presetType): RuntimeException
    {
        return new RuntimeException(sprintf('Unknown preset type "%s".', $presetType));
    }
}
