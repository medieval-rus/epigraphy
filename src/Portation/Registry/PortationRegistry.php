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

namespace App\Portation\Registry;

use App\Portation\Exporter\ExporterInterface;
use App\Portation\Importer\ImporterInterface;
use InvalidArgumentException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class PortationRegistry implements PortationRegistryInterface
{
    /**
     * @var ExporterInterface[]
     */
    private $exporters = [];

    /**
     * @var ImporterInterface[]
     */
    private $importers = [];

    public function addExporter(string $format, ExporterInterface $exporter): void
    {
        if (\array_key_exists($format, $this->exporters)) {
            throw new InvalidArgumentException(sprintf('Duplicate exporter registered for format "%s"', $format));
        }

        $this->exporters[$format] = $exporter;
    }

    public function addImporter(string $format, ImporterInterface $importer): void
    {
        if (\array_key_exists($format, $this->importers)) {
            throw new InvalidArgumentException(sprintf('Duplicate importer registered for format "%s"', $format));
        }

        $this->importers[$format] = $importer;
    }

    public function getExporter(string $format): ExporterInterface
    {
        if (!\array_key_exists($format, $this->exporters)) {
            throw new InvalidArgumentException(sprintf('Unknown export format "%s"', $format));
        }

        return $this->exporters[$format];
    }

    public function getImporter(string $format): ImporterInterface
    {
        if (!\array_key_exists($format, $this->importers)) {
            throw new InvalidArgumentException(sprintf('Unknown import format "%s"', $format));
        }

        return $this->importers[$format];
    }
}
