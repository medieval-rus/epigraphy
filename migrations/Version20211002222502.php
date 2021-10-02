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

final class Version20211002222502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added file field to bibliographic records table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bibliography__bibliographic_record ADD main_file_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bibliography__bibliographic_record ADD CONSTRAINT FK_A98E1C236780D085 FOREIGN KEY (main_file_id) REFERENCES file (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A98E1C236780D085 ON bibliography__bibliographic_record (main_file_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bibliography__bibliographic_record DROP FOREIGN KEY FK_A98E1C236780D085');
        $this->addSql('DROP INDEX UNIQ_A98E1C236780D085 ON bibliography__bibliographic_record');
        $this->addSql('ALTER TABLE bibliography__bibliographic_record DROP main_file_id');
    }
}
