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

final class Version20210926011132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Bibliographic record refactoring.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_2EC905B5E237E06 ON bibliography__references_list');
        $this->addSql('ALTER TABLE bibliography__references_list_item DROP FOREIGN KEY FK_E0644C53B8550050');
        $this->addSql('DROP INDEX bibliographic_record_list_has_bibliographic_record ON bibliography__references_list_item');
        $this->addSql('DROP INDEX IDX_2273013DB8550050 ON bibliography__references_list_item');
        $this->addSql('ALTER TABLE bibliography__references_list_item CHANGE bibliographic_record_list_id references_list_id INT NOT NULL');
        $this->addSql('ALTER TABLE bibliography__references_list_item ADD CONSTRAINT FK_2273013DF529DD3E FOREIGN KEY (references_list_id) REFERENCES bibliography__references_list (id)');
        $this->addSql('CREATE INDEX IDX_2273013DF529DD3E ON bibliography__references_list_item (references_list_id)');
        $this->addSql('CREATE UNIQUE INDEX record_is_unique_within_list ON bibliography__references_list_item (references_list_id, bibliographic_record_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2EC905B5E237E06 ON bibliography__references_list (name)');
        $this->addSql('ALTER TABLE bibliography__references_list_item DROP FOREIGN KEY FK_2273013DF529DD3E');
        $this->addSql('DROP INDEX IDX_2273013DF529DD3E ON bibliography__references_list_item');
        $this->addSql('DROP INDEX record_is_unique_within_list ON bibliography__references_list_item');
        $this->addSql('ALTER TABLE bibliography__references_list_item CHANGE references_list_id bibliographic_record_list_id INT NOT NULL');
        $this->addSql('ALTER TABLE bibliography__references_list_item ADD CONSTRAINT FK_E0644C53B8550050 FOREIGN KEY (bibliographic_record_list_id) REFERENCES bibliography__references_list (id)');
        $this->addSql('CREATE UNIQUE INDEX bibliographic_record_list_has_bibliographic_record ON bibliography__references_list_item (bibliographic_record_list_id, bibliographic_record_id)');
        $this->addSql('CREATE INDEX IDX_2273013DB8550050 ON bibliography__references_list_item (bibliographic_record_list_id)');
    }
}
