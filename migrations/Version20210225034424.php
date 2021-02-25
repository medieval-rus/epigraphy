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

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210225034424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE bibliography__record TO bibliography__bibliographic_record');
        $this->addSql('RENAME TABLE bibliography__references_list__item TO bibliography__references_list_item');
        $this->addSql('RENAME TABLE bibliography__references_list__references_list TO bibliography__references_list');
        $this->addSql('ALTER TABLE bibliography__bibliographic_record RENAME INDEX uniq_3a8e6ae83ee4b093 TO UNIQ_A98E1C233EE4B093');
        $this->addSql('ALTER TABLE bibliography__references_list RENAME INDEX uniq_865d5cee5e237e06 TO UNIQ_2EC905B5E237E06');
        $this->addSql('ALTER TABLE bibliography__references_list_item RENAME INDEX idx_e0644c53b8550050 TO IDX_2273013DB8550050');
        $this->addSql('ALTER TABLE bibliography__references_list_item RENAME INDEX idx_e0644c537aca5d3f TO IDX_2273013D7ACA5D3F');
        $this->addSql('CREATE TABLE bibliographic_record_author (bibliographic_record_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_2389DB9A7ACA5D3F (bibliographic_record_id), INDEX IDX_2389DB9AF675F31B (author_id), PRIMARY KEY(bibliographic_record_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bibliographic_record_author ADD CONSTRAINT FK_2389DB9A7ACA5D3F FOREIGN KEY (bibliographic_record_id) REFERENCES bibliography__bibliographic_record (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bibliographic_record_author ADD CONSTRAINT FK_2389DB9AF675F31B FOREIGN KEY (author_id) REFERENCES bibliography__author (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO bibliographic_record_author (bibliographic_record_id, author_id) SELECT bibliographic_record_id, author_id FROM bibliography__authorship');
        $this->addSql('DROP TABLE bibliography__authorship');
        $this->addSql('RENAME TABLE bibliographic_record_author TO bibliography__bibliographic_record_author');
        $this->addSql('ALTER TABLE bibliography__bibliographic_record_author RENAME INDEX idx_2389db9a7aca5d3f TO IDX_F06886D67ACA5D3F');
        $this->addSql('ALTER TABLE bibliography__bibliographic_record_author RENAME INDEX idx_2389db9af675f31b TO IDX_F06886D6F675F31B');
    }
}
