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

namespace App\Command\Portation;

use App\Portation\Registry\PortationRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ExportCommand extends Command
{
    /**
     * @var PortationRegistryInterface
     */
    private $portationRegistry;

    /**
     * @param PortationRegistryInterface $portationRegistry
     */
    public function __construct(PortationRegistryInterface $portationRegistry)
    {
        parent::__construct();
        $this->portationRegistry = $portationRegistry;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:portation:export')
            ->setDescription('Export data from database to human-readable format')
            ->addArgument('export-format', InputArgument::REQUIRED, 'Export format')
            ->addArgument('export-file', InputArgument::REQUIRED, 'Path to export file')
            ->addArgument('bunch-size', InputArgument::OPTIONAL, 'Bunch size')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bunchSizeArgument = $input->getArgument('bunch-size');

        $this
            ->portationRegistry
            ->getExporter($input->getArgument('export-format'))
            ->export($input->getArgument('export-file'), null === $bunchSizeArgument ? null : (int) $bunchSizeArgument);

        (new SymfonyStyle($input, $output))->success('Export has been successfully finished');

        return 0;
    }
}
