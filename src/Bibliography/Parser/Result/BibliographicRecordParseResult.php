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

namespace App\Bibliography\Parser\Result;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class BibliographicRecordParseResult
{
    /**
     * @var string
     */
    private $shortName;

    /**
     * @var string[]
     */
    private $authors;

    /**
     * @var string
     */
    private $publicationName;

    /**
     * @var string
     */
    private $publicationDetails;

    /**
     * @var int
     */
    private $year;

    public function __construct(
        string $shortName,
        array $authors,
        string $publicationName,
        string $publicationDetails,
        int $year
    ) {
        $this->shortName = $shortName;
        $this->authors = $authors;
        $this->publicationName = $publicationName;
        $this->publicationDetails = $publicationDetails;
        $this->year = $year;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @return string[]
     */
    public function getAuthors(): array
    {
        return $this->authors;
    }

    public function getPublicationName(): string
    {
        return $this->publicationName;
    }

    public function getPublicationDetails(): string
    {
        return $this->publicationDetails;
    }

    public function getYear(): int
    {
        return $this->year;
    }
}
