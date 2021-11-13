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

final class Version20211113010450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Inscription list.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE inscription_list (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription_list__item (inscription_list_id INT NOT NULL, inscription_id INT NOT NULL, INDEX IDX_88674C92147FE067 (inscription_list_id), INDEX IDX_88674C925DAC5993 (inscription_id), PRIMARY KEY(inscription_list_id, inscription_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inscription_list__item ADD CONSTRAINT FK_88674C92147FE067 FOREIGN KEY (inscription_list_id) REFERENCES inscription_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inscription_list__item ADD CONSTRAINT FK_88674C925DAC5993 FOREIGN KEY (inscription_id) REFERENCES inscription (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inscription ADD is_shown_on_site TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('UPDATE inscription SET is_shown_on_site = \'1\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inscription_list__item DROP FOREIGN KEY FK_88674C92147FE067');
        $this->addSql('DROP TABLE inscription_list');
        $this->addSql('DROP TABLE inscription_list__item');
        $this->addSql('ALTER TABLE inscription DROP is_shown_on_site');
    }
}
