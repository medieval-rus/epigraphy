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

namespace App\Command;

use App\Persistence\Entity\Media\File;
use App\Services\Media\Thumbnails\ThumbnailsGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class GenerateThumbnailsCommand extends Command
{
    protected static $defaultName = 'app:generate-thumbnails';
    protected static $defaultDescription = 'Generate thumbnails for all the media content';

    private EntityManagerInterface $doctrine;

    private ThumbnailsGeneratorInterface $thumbnailsGenerator;

    public function __construct(
        EntityManagerInterface $doctrine,
        ThumbnailsGeneratorInterface $thumbnailsGenerator
    ) {
        parent::__construct();

        $this->doctrine = $doctrine;
        $this->thumbnailsGenerator = $thumbnailsGenerator;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Generating thumbnails.');

        $files = $this->doctrine->getRepository(File::class)->findAll();

        foreach ($files as $file) {
            $this->thumbnailsGenerator->generateAll($file);
        }

        $io->success('Thumbnails successfully generated.');

        return Command::SUCCESS;
    }
}
