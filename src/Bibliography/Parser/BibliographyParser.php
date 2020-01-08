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

namespace App\Bibliography\Parser;

use App\Bibliography\Parser\Result\BibliographicRecordParseResult;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use LogicException;
use Vyfony\Bundle\BibliographyBundle\Persistence\Entity\Author;
use Vyfony\Bundle\BibliographyBundle\Persistence\Entity\Authorship;
use Vyfony\Bundle\BibliographyBundle\Persistence\Entity\BibliographicRecord;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class BibliographyParser implements BibliographyParserInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function parse(string $rawBibliography): void
    {
        $rawBibliographicRecords = explode(PHP_EOL, $rawBibliography);

        array_pop($rawBibliographicRecords);

        $parsedBibliographicRecords = array_map([$this, 'parserBibliographicRecord'], $rawBibliographicRecords);

        $this->saveAllAuthors($parsedBibliographicRecords);

        foreach ($parsedBibliographicRecords as $bibliographicRecord) {
            $this->saveBibliographicRecord($bibliographicRecord);
        }

        $this->entityManager->flush();
    }

    private function parserBibliographicRecord(string $formattedRecord): BibliographicRecordParseResult
    {
        $match = preg_match_all('/^(.+)\\{(.*)%(.+)\\}(.+)$/', $formattedRecord, $matches);

        if (false === $match || 0 === $match) {
            throw new InvalidArgumentException(sprintf('Cannot parser record "%s"', $formattedRecord));
        }

        $formattedAuthors = $matches[2][0];

        return new BibliographicRecordParseResult(
            $matches[1][0],
            '' !== $formattedAuthors ? explode(', ', $formattedAuthors) : [],
            $matches[3][0],
            $matches[4][0],
            $this->parseYear($matches[4][0])
        );
    }

    private function saveBibliographicRecord(BibliographicRecordParseResult $parseResult): void
    {
        $record = new BibliographicRecord();

        $this->entityManager->persist($record);

        $record->setShortName($parseResult->getShortName());
        $record->setAuthors(implode(', ', $parseResult->getAuthors()));
        $record->setTitle($parseResult->getPublicationName());
        $record->setDetails($parseResult->getPublicationDetails());
        $record->setYear($parseResult->getYear());

        $authorPosition = 0;

        foreach ($parseResult->getAuthors() as $authorName) {
            $referenceAuthorName = $this->getReferenceAuthorName($authorName);

            $author = $this
                ->entityManager
                ->getRepository(Author::class)
                ->findOneBy(['fullName' => $referenceAuthorName]);

            if (null === $author) {
                throw new LogicException(sprintf('Author with name "%s" not found', $referenceAuthorName));
            }

            $authorship = new Authorship();

            $this->entityManager->persist($authorship);

            $authorship->setBibliographicRecord($record);
            $authorship->setAuthor($author);
            $authorship->setPosition($authorPosition++);
        }
    }

    private function getAllAuthors(array $parsedBibliographicRecords): array
    {
        $authors = array_merge(
            ...array_map(
                function (BibliographicRecordParseResult $parseResult): array {
                    return $parseResult->getAuthors();
                },
                $parsedBibliographicRecords
            )
        );

        $filteredAuthors = array_filter($authors, function (string $author): bool {
            return '' !== $author;
        });

        $authorsWithReferenceNames = array_map(function (string $authorsName): string {
            return $this->getReferenceAuthorName($authorsName);
        }, $filteredAuthors);

        $uniqueAuthors = array_unique($authorsWithReferenceNames);

        sort($uniqueAuthors);

        return $uniqueAuthors;
    }

    private function getReferenceAuthorName(string $authorName): string
    {
        $knownMappings = [
            'A. A. Gippius' => 'А. А. Гиппиус',
            'А. Гиппиус' => 'А. А. Гиппиус',
            'S. M. Mikheev' => 'С. М. Михеев',
            'T. V. Roždestvenskaja' => 'Т. В. Рождественская',
            'І. Л. Калечыц' => 'И. Л. Калечиц',
            'В. В. Корнієнко' => 'В. В. Корниенко',
            'В. Корнієнко' => 'В. В. Корниенко',
            'И. Зайцев' => 'И. В. Зайцев',
            'И. Срезневский' => 'И. И. Срезневский',
            'Н. Воронин' => 'Н. Н. Воронин',
            'Н. Нікітенко' => 'Н. Н. Никитенко',
            'Н. М. Нікітенко' => 'Н. Н. Никитенко',
            'Т. Рождественская' => 'Т. В. Рождественская',
            'И. Шляпкин' => 'И. А. Шляпкин',
            'Шляпкин И. А.' => 'И. А. Шляпкин',
            'Ю. Артамонов' => 'Ю. А. Артамонов',
            'А. О[ленин]' => 'А. Оленин',
            '[А. В. Арциховский]' => 'А. В. Арциховский',
            'В. [М.] Ж[ивов]' => 'В. М. Живов',
            '[В. Л. Янин]' => 'В. Л. Янин',
            'Д. Ёлшин' => 'Д. Д. Йолшин',
            'Ёлшин Д. Д.' => 'Д. Д. Йолшин',
            'Ивакин Г. Ю.' => 'Г. Ю. Ивакин',
            'Г. Ю. Івакін' => 'Г. Ю. Ивакин',
            'Иоаннисян О. М.' => 'О. М. Иоаннисян',
            'Зыков П. Л.' => 'П. Л. Зыков',
            'Козюба В. К.' => 'В. К. Козюба',
            'О. В. Комар' => 'А. В. Комар',
            'V. Orel' => 'В. Э. Орел',
            'Б. Рибаков' => 'Б. А. Рыбаков',
        ];

        if (\array_key_exists($authorName, $knownMappings)) {
            return $knownMappings[$authorName];
        }

        return $authorName;
    }

    private function saveAllAuthors(array $parsedBibliographicRecords): void
    {
        $authorsNames = $this->getAllAuthors($parsedBibliographicRecords);

        foreach ($authorsNames as $authorName) {
            $author = new Author();

            $author->setFullName($authorName);

            $this->entityManager->persist($author);
        }

        $this->entityManager->flush();
    }

    private function parseYear(string $publicationDetails): int
    {
        $match = preg_match_all('/(\d{4})/', $publicationDetails, $matches);

        if (false === $match || 0 === $match) {
            $message = sprintf('Cannot parse year from publication details "%s"', $publicationDetails);

            throw new InvalidArgumentException($message);
        }

        return (int) array_pop($matches[1]);
    }
}
