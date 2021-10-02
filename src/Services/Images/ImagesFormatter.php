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

namespace App\Services\Images;

use App\Models\FilesActualValue;
use App\Persistence\Entity\Epigraphy\Inscription;
use App\Persistence\Entity\Media\File;
use App\Services\ActualValue\Extractor\ActualValueExtractorInterface;
use App\Services\Media\Thumbnails\ThumbnailsGeneratorInterface;

final class ImagesFormatter implements ImagesFormatterInterface
{
    private ActualValueExtractorInterface $extractor;

    private ThumbnailsGeneratorInterface $thumbnailsGenerator;

    public function __construct(
        ActualValueExtractorInterface $extractor,
        ThumbnailsGeneratorInterface $thumbnailsGenerator
    ) {
        $this->extractor = $extractor;
        $this->thumbnailsGenerator = $thumbnailsGenerator;
    }

    public function formatZeroRowImages(Inscription $inscription, string $propertyName): string
    {
        return implode(
            '<br>',
            array_map(
                [$this, 'formatImages'],
                $this->extractor->extractFromZeroRowAsFiles($inscription, $propertyName)
            )
        );
    }

    public function formatInscriptionImages(Inscription $inscription, string $propertyName): string
    {
        $file = $this->extractor->extractFromInscriptionAsFile($inscription, $propertyName);

        if (null === $file) {
            return '';
        }

        return $this->formatImages($file);
    }

    private function formatImages(FilesActualValue $actualValue): string
    {
        $files = $actualValue->getValue();

        $imageTags = array_map(
            function (File $file): string {
                $createImageTag = function () use ($file): string {
                    if (null === $file->getUrl()) {
                        return '<img class="eomr-images-image" alt="'.$file->getFileName().'" />';
                    }

                    $thumbnailUrl = $this->thumbnailsGenerator->getThumbnail($file, 'large');

                    return '<a class="eomr-images-image-link" href="'.$file->getUrl().'" target="_blank">'.
                        '<img class="eomr-images-image" src="'.$thumbnailUrl.'" alt="'.$file->getFileName().'" />'.
                        '</a>';
                };

                $createDescriptionTag = static function () use ($file): string {
                    return '<div class="eomr-images-description">'.$file->getDescription().'</div>';
                };

                return '<div class="eomr-images-image">'.
                    $createImageTag().
                    $createDescriptionTag().
                    '</div>';
            },
            $files
        );

        return '<div class="eomr-images-wrapper">'.
            implode('<br>', $imageTags).
            '</div>';
    }
}
