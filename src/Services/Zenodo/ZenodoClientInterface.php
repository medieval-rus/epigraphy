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

namespace App\Services\Zenodo;

interface ZenodoClientInterface
{
    public function getLatestDepositionIdVersion(string $recordId): string;

    /**
     * @param string[] $keywords
     */
    public function createImagesDeposition(
        string $title,
        string $description,
        array $keywords,
        array $communities,
        array $creators
    ): array;

    public function saveFile(string $fileName, string $file, string $depositionId): array;

    public function removeFile(string $fileId, string $depositionId): void;

    public function publishDeposition(string $depositionId): void;

    public function newVersion(string $depositionId): string;

    public function deleteVersion(string $depositionId): void;

    public function createAndPublishImagesDeposition(
        string $title,
        string $description,
        array $keywords,
        array $communities,
        array $creators,
        string $readmeContent
    ): string;
}
