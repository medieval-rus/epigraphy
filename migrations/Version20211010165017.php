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

final class Version20211010165017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Created relation between interpretation and bibliographic record.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE interpretation ADD source_id INT NULL');
        $this->addSql('UPDATE interpretation i JOIN bibliography__bibliographic_record br ON i.source = br.short_name SET i.source_id = br.id');
        $this->addSql('ALTER TABLE interpretation CHANGE source_id source_id INT NOT NULL');
        $this->addSql('ALTER TABLE interpretation DROP source');
        $this->addSql('ALTER TABLE interpretation ADD CONSTRAINT FK_EBDBD117953C1C61 FOREIGN KEY (source_id) REFERENCES bibliography__bibliographic_record (id)');
        $this->addSql('CREATE INDEX IDX_EBDBD117953C1C61 ON interpretation (source_id)');
        $this->addSql('CREATE UNIQUE INDEX source_is_unique_within_inscription ON interpretation (inscription_id, source_id)');
    }
}
