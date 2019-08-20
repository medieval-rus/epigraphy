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
final class Version20190820205012 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Initial database creation';
    }

    /**
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, carrier_id INT NOT NULL, writing_type_id INT NOT NULL, writing_method_id INT NOT NULL, preservation_state_id INT NOT NULL, alphabet_id INT DEFAULT NULL, content_category_id INT DEFAULT NULL, is_in_situ TINYINT(1) DEFAULT \'1\' NOT NULL, place_on_carrier VARCHAR(255) DEFAULT NULL, text LONGTEXT DEFAULT NULL, new_text LONGTEXT DEFAULT NULL, transliteration LONGTEXT DEFAULT NULL, translation LONGTEXT DEFAULT NULL, date_in_text VARCHAR(255) DEFAULT NULL, comment_on_date LONGTEXT DEFAULT NULL, comment_on_text LONGTEXT DEFAULT NULL, INDEX IDX_5E90F6D621DFC797 (carrier_id), INDEX IDX_5E90F6D6E7360910 (writing_type_id), INDEX IDX_5E90F6D694F4A73A (writing_method_id), INDEX IDX_5E90F6D6FE71FA16 (preservation_state_id), INDEX IDX_5E90F6D686D95EE5 (alphabet_id), INDEX IDX_5E90F6D6416C3764 (content_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription_material (inscription_id INT NOT NULL, material_id INT NOT NULL, INDEX IDX_D52B0A305DAC5993 (inscription_id), INDEX IDX_D52B0A30E308AC6F (material_id), PRIMARY KEY(inscription_id, material_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE material (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carrier (id INT AUTO_INCREMENT NOT NULL, building_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, carrier_type VARCHAR(255) NOT NULL, INDEX IDX_4739F11C4D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE writing_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE building (id INT AUTO_INCREMENT NOT NULL, building_type_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E16F61D4F28401B9 (building_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE building_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE preservation_state (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE alphabet (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE writing_method (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D621DFC797 FOREIGN KEY (carrier_id) REFERENCES carrier (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6E7360910 FOREIGN KEY (writing_type_id) REFERENCES writing_type (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D694F4A73A FOREIGN KEY (writing_method_id) REFERENCES writing_method (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6FE71FA16 FOREIGN KEY (preservation_state_id) REFERENCES preservation_state (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D686D95EE5 FOREIGN KEY (alphabet_id) REFERENCES alphabet (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6416C3764 FOREIGN KEY (content_category_id) REFERENCES content_category (id)');
        $this->addSql('ALTER TABLE inscription_material ADD CONSTRAINT FK_D52B0A305DAC5993 FOREIGN KEY (inscription_id) REFERENCES inscription (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inscription_material ADD CONSTRAINT FK_D52B0A30E308AC6F FOREIGN KEY (material_id) REFERENCES material (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrier ADD CONSTRAINT FK_4739F11C4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D4F28401B9 FOREIGN KEY (building_type_id) REFERENCES building_type (id)');
    }

    /**
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE inscription_material DROP FOREIGN KEY FK_D52B0A305DAC5993');
        $this->addSql('ALTER TABLE inscription_material DROP FOREIGN KEY FK_D52B0A30E308AC6F');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D621DFC797');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6E7360910');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6416C3764');
        $this->addSql('ALTER TABLE carrier DROP FOREIGN KEY FK_4739F11C4D2A7E12');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D4F28401B9');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6FE71FA16');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D686D95EE5');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D694F4A73A');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE inscription_material');
        $this->addSql('DROP TABLE material');
        $this->addSql('DROP TABLE carrier');
        $this->addSql('DROP TABLE writing_type');
        $this->addSql('DROP TABLE content_category');
        $this->addSql('DROP TABLE building');
        $this->addSql('DROP TABLE building_type');
        $this->addSql('DROP TABLE preservation_state');
        $this->addSql('DROP TABLE alphabet');
        $this->addSql('DROP TABLE writing_method');
    }
}
