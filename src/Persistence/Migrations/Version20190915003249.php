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

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class Version20190915003249 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Added interpretation table';
    }

    /**
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE interpretation (id INT AUTO_INCREMENT NOT NULL, inscription_id INT NOT NULL, source LONGTEXT NOT NULL, do_we_agree TINYINT(1) DEFAULT \'0\' NOT NULL, text LONGTEXT DEFAULT NULL, text_image_file_name VARCHAR(255) DEFAULT NULL, transliteration LONGTEXT DEFAULT NULL, translation LONGTEXT DEFAULT NULL, photo_file_name VARCHAR(255) DEFAULT NULL, sketch_file_name VARCHAR(255) DEFAULT NULL, date VARCHAR(255) DEFAULT NULL, comment_file_name VARCHAR(255) DEFAULT NULL, INDEX IDX_EBDBD1175DAC5993 (inscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interpretation ADD CONSTRAINT FK_EBDBD1175DAC5993 FOREIGN KEY (inscription_id) REFERENCES inscription (id)');
        $this->addSql('ALTER TABLE inscription DROP text, DROP new_text, DROP transliteration, DROP translation, DROP comment_on_date, DROP comment_on_text, CHANGE carrier_id carrier_id INT DEFAULT NULL, CHANGE writing_type_id writing_type_id INT DEFAULT NULL, CHANGE writing_method_id writing_method_id INT DEFAULT NULL, CHANGE preservation_state_id preservation_state_id INT DEFAULT NULL, CHANGE is_in_situ is_in_situ TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE carrier CHANGE name name VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE interpretation');
        $this->addSql('ALTER TABLE carrier CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE inscription ADD text LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD new_text LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD transliteration LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD translation LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD comment_on_date LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD comment_on_text LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE carrier_id carrier_id INT NOT NULL, CHANGE writing_type_id writing_type_id INT NOT NULL, CHANGE writing_method_id writing_method_id INT NOT NULL, CHANGE preservation_state_id preservation_state_id INT NOT NULL, CHANGE is_in_situ is_in_situ TINYINT(1) DEFAULT \'1\' NOT NULL');
    }
}
