<?php

declare(strict_types=1);

/*
 * This file is part of «Epigraphy of Medieval Rus'» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus'» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus'» database is distributed
 * in the hope  that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus'» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\Services\Images;

use App\Models\ActualValue;
use App\Persistence\Entity\Epigraphy\Inscription;
use App\Services\ActualValue\Extractor\ActualValueExtractorInterface;
use App\Services\GoogleDrive\FileUrlGetter\FileUrlGetterInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ImagesFormatter implements ImagesFormatterInterface
{
    /**
     * @var ActualValueExtractorInterface
     */
    private $extractor;

    /**
     * @var FileUrlGetterInterface
     */
    private $fileUrlGetter;

    public function __construct(
        ActualValueExtractorInterface $extractor,
        FileUrlGetterInterface $fileUrlGetter
    ) {
        $this->extractor = $extractor;
        $this->fileUrlGetter = $fileUrlGetter;
    }

    public function format(Inscription $inscription, string $propertyName): string
    {
        return implode(
            '<br>',
            array_map(
                function (ActualValue $actualValue): string {
                    $fileNames = explode(', ', $actualValue->getValue());

                    $fileUrls = array_map([$this->fileUrlGetter, 'getFileUrl'], $fileNames);

                    $imagesTags = array_map(
                        static function (?string $fileUrl, string $fileName): string {
                            if (null === $fileUrl) {
                                return '<img alt="'.$fileName.'" />';
                            }

                            return '<a class="eomr-image-link" href="'.$fileUrl.'" target="_blank">'.
                                '<img class="eomr-image" src="'.$fileUrl.'" alt="'.$fileName.'" />'.
                                '</a>';
                        },
                        $fileUrls,
                        $fileNames
                    );

                    $sourceBlock = null !== $actualValue->getSource()
                        ? '<div class="eomr-images-source">'.$actualValue->getSource().'</div>'
                        : '';

                    return '<div class="eomr-images-of-source">'.
                        '<div class="eomr-images">'.implode('<br>', $imagesTags).'</div>'.
                        $sourceBlock.
                        '</div>';
                },
                $this->extractor->extract($inscription, $propertyName)
            )
        );
    }
}
