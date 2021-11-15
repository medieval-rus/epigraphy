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

final class Version20211115020407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Renamed content to description.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE zero_row_content_references TO zero_row_description_references');
        $this->addSql('ALTER TABLE interpretation CHANGE content description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE zero_row CHANGE content description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE zero_row_description_references RENAME INDEX idx_f1a8a1c870a77ce4 TO IDX_FECC3FF970A77CE4');
        $this->addSql('ALTER TABLE zero_row_description_references RENAME INDEX idx_f1a8a1c8f60a8f2c TO IDX_FECC3FF9F60A8F2C');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE zero_row_description_references TO zero_row_content_references');
        $this->addSql('ALTER TABLE interpretation CHANGE description content TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE zero_row CHANGE description content TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE zero_row_description_references RENAME INDEX idx_fecc3ff970a77ce4 TO IDX_F1A8A1C870A77CE4');
        $this->addSql('ALTER TABLE zero_row_description_references RENAME INDEX idx_fecc3ff9f60a8f2c TO IDX_F1A8A1C8F60A8F2C');
    }
}
