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

namespace App\Portation\Exporter\Xlsx\Drawer;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
interface XlsxDrawerInterface
{
    /**
     * @param string    $cellValue
     * @param int       $columnIndex
     * @param int       $rowIndex
     * @param Worksheet $sheet
     */
    public function drawCell(string $cellValue, int $columnIndex, int $rowIndex, Worksheet $sheet): void;

    /**
     * @param string[]  $cellValues
     * @param int       $columnIndex
     * @param int       $rowIndex
     * @param Worksheet $sheet
     */
    public function drawRow(array $cellValues, int $columnIndex, int $rowIndex, Worksheet $sheet): void;
}
