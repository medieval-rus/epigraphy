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

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210926033158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Renamed indexes for drawings table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inscription_drawings RENAME INDEX idx_8d638b575dac5993 TO IDX_8C6CACEA5DAC5993');
        $this->addSql('ALTER TABLE inscription_drawings RENAME INDEX idx_8d638b5793cb796c TO IDX_8C6CACEA93CB796C');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inscription_drawings RENAME INDEX idx_8c6cacea5dac5993 TO IDX_8D638B575DAC5993');
        $this->addSql('ALTER TABLE inscription_drawings RENAME INDEX idx_8c6cacea93cb796c TO IDX_8D638B5793CB796C');
    }
}
