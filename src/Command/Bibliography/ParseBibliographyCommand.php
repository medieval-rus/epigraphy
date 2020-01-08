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

namespace App\Command\Bibliography;

use App\Bibliography\Parser\BibliographyParserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ParseBibliographyCommand extends Command
{
    protected static $defaultName = 'app:parse-bibliography';

    /**
     * @var BibliographyParserInterface
     */
    private $parser;

    public function __construct(BibliographyParserInterface $parser)
    {
        parent::__construct();

        $this->parser = $parser;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Parses project bibliography')
            ->addArgument('source-file', InputArgument::REQUIRED, 'Path to source file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $pathToSourceFile = $input->getArgument('source-file');

        $this->parser->parse(file_get_contents($pathToSourceFile));

        $io->success('Bibliography successfully parsed');

        return 0;
    }
}
