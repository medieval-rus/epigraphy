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

final class Version20210227000541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added "origin" column to interpretation and zero row.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE zero_row_origin_references (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_CBA88C4570A77CE4 (zero_row_id), INDEX IDX_CBA88C45F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE zero_row_origin_references ADD CONSTRAINT FK_CBA88C4570A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_origin_references ADD CONSTRAINT FK_CBA88C45F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interpretation ADD origin LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE zero_row ADD origin LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE zero_row_origin_references');
        $this->addSql('ALTER TABLE interpretation DROP origin');
        $this->addSql('ALTER TABLE zero_row DROP origin');
    }
}
