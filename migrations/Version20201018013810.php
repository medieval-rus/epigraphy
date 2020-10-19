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

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class Version20201018013810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates full data structure.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE alphabet (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_103ECD7F5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bibliography__author (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1C152DB1DBC463C4 (full_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bibliography__authorship (id INT AUTO_INCREMENT NOT NULL, bibliographic_record_id INT NOT NULL, author_id INT NOT NULL, position INT NOT NULL, INDEX IDX_4CDFAD337ACA5D3F (bibliographic_record_id), INDEX IDX_4CDFAD33F675F31B (author_id), UNIQUE INDEX UNIQ_4CDFAD337ACA5D3FF675F31B (bibliographic_record_id, author_id), UNIQUE INDEX UNIQ_4CDFAD337ACA5D3F462CE4F5 (bibliographic_record_id, position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bibliography__record (id INT AUTO_INCREMENT NOT NULL, short_name VARCHAR(255) NOT NULL, authors VARCHAR(255) DEFAULT NULL, title TEXT NOT NULL, details TEXT NOT NULL, year INT NOT NULL, UNIQUE INDEX UNIQ_3A8E6AE83EE4B093 (short_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bibliography__references_list__item (id INT AUTO_INCREMENT NOT NULL, bibliographic_record_list_id INT NOT NULL, bibliographic_record_id INT NOT NULL, position INT NOT NULL, INDEX IDX_E0644C53B8550050 (bibliographic_record_list_id), INDEX IDX_E0644C537ACA5D3F (bibliographic_record_id), UNIQUE INDEX bibliographic_record_list_has_bibliographic_record (bibliographic_record_list_id, bibliographic_record_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bibliography__references_list__references_list (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_865D5CEE5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carrier (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, category_id INT DEFAULT NULL, origin1 VARCHAR(255) DEFAULT NULL, origin2 VARCHAR(255) DEFAULT NULL, find_circumstances LONGTEXT DEFAULT NULL, characteristics LONGTEXT DEFAULT NULL, individual_name VARCHAR(255) DEFAULT NULL, storage_place VARCHAR(255) DEFAULT NULL, inventory_number VARCHAR(255) DEFAULT NULL, is_in_situ TINYINT(1) DEFAULT NULL, INDEX IDX_4739F11CC54C8C93 (type_id), INDEX IDX_4739F11C12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carrier_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_7E1F23455E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carrier_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_DEB32BAE5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_54FBF32E5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, carrier_id INT DEFAULT NULL, zero_row_id INT DEFAULT NULL, conventional_date VARCHAR(255) DEFAULT NULL, INDEX IDX_5E90F6D621DFC797 (carrier_id), UNIQUE INDEX UNIQ_5E90F6D670A77CE4 (zero_row_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interpretation (id INT AUTO_INCREMENT NOT NULL, writing_type_id INT DEFAULT NULL, writing_method_id INT DEFAULT NULL, preservation_state_id INT DEFAULT NULL, alphabet_id INT DEFAULT NULL, content_category_id INT DEFAULT NULL, inscription_id INT NOT NULL, place_on_carrier LONGTEXT DEFAULT NULL, text LONGTEXT DEFAULT NULL, text_image_file_names VARCHAR(255) DEFAULT NULL, transliteration LONGTEXT DEFAULT NULL, translation LONGTEXT DEFAULT NULL, photo_file_names VARCHAR(255) DEFAULT NULL, sketch_file_names VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, date_in_text LONGTEXT DEFAULT NULL, stratigraphical_date LONGTEXT DEFAULT NULL, non_stratigraphical_date LONGTEXT DEFAULT NULL, historical_date LONGTEXT DEFAULT NULL, source VARCHAR(255) NOT NULL, comment LONGTEXT DEFAULT NULL, page_numbers_in_source VARCHAR(255) DEFAULT NULL, number_in_source VARCHAR(255) DEFAULT NULL, INDEX IDX_EBDBD117E7360910 (writing_type_id), INDEX IDX_EBDBD11794F4A73A (writing_method_id), INDEX IDX_EBDBD117FE71FA16 (preservation_state_id), INDEX IDX_EBDBD11786D95EE5 (alphabet_id), INDEX IDX_EBDBD117416C3764 (content_category_id), INDEX IDX_EBDBD1175DAC5993 (inscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interpretation_material (interpretation_id INT NOT NULL, material_id INT NOT NULL, INDEX IDX_852FD531F60A8F2C (interpretation_id), INDEX IDX_852FD531E308AC6F (material_id), PRIMARY KEY(interpretation_id, material_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE material (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_7CBE75955E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE preservation_state (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8770A8E45E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, full_name VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE writing_method (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_26CA00D65E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE writing_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1BF19BA05E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row (id INT AUTO_INCREMENT NOT NULL, writing_type_id INT DEFAULT NULL, writing_method_id INT DEFAULT NULL, preservation_state_id INT DEFAULT NULL, alphabet_id INT DEFAULT NULL, content_category_id INT DEFAULT NULL, place_on_carrier LONGTEXT DEFAULT NULL, text LONGTEXT DEFAULT NULL, text_image_file_names VARCHAR(255) DEFAULT NULL, transliteration LONGTEXT DEFAULT NULL, translation LONGTEXT DEFAULT NULL, photo_file_names VARCHAR(255) DEFAULT NULL, sketch_file_names VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, date_in_text LONGTEXT DEFAULT NULL, stratigraphical_date LONGTEXT DEFAULT NULL, non_stratigraphical_date LONGTEXT DEFAULT NULL, historical_date LONGTEXT DEFAULT NULL, INDEX IDX_34AB4D18E7360910 (writing_type_id), INDEX IDX_34AB4D1894F4A73A (writing_method_id), INDEX IDX_34AB4D18FE71FA16 (preservation_state_id), INDEX IDX_34AB4D1886D95EE5 (alphabet_id), INDEX IDX_34AB4D18416C3764 (content_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_place_on_carrier (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_FA2C803D70A77CE4 (zero_row_id), INDEX IDX_FA2C803DF60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_writing_type (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_B22625EE70A77CE4 (zero_row_id), INDEX IDX_B22625EEF60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_writing_method (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_64E9550170A77CE4 (zero_row_id), INDEX IDX_64E95501F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_preservation_state (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_70FF924270A77CE4 (zero_row_id), INDEX IDX_70FF9242F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_alphabet (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_41397D6570A77CE4 (zero_row_id), INDEX IDX_41397D65F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_text (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_3147CA0970A77CE4 (zero_row_id), INDEX IDX_3147CA09F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_text_image_file_names (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_1D0A6FAF70A77CE4 (zero_row_id), INDEX IDX_1D0A6FAFF60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_transliteration (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_FEC2069070A77CE4 (zero_row_id), INDEX IDX_FEC20690F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_translation (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_DF79588370A77CE4 (zero_row_id), INDEX IDX_DF795883F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_photo_file_names (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_C1ACA3FF70A77CE4 (zero_row_id), INDEX IDX_C1ACA3FFF60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_sketch_file_names (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_3F57EA5770A77CE4 (zero_row_id), INDEX IDX_3F57EA57F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_content_category (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_818347AE70A77CE4 (zero_row_id), INDEX IDX_818347AEF60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_content (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_2775109870A77CE4 (zero_row_id), INDEX IDX_27751098F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_date_in_text (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_8100211070A77CE4 (zero_row_id), INDEX IDX_81002110F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_stratigraphical_date (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_B63AA8F070A77CE4 (zero_row_id), INDEX IDX_B63AA8F0F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_non_stratigraphical_date (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_B47CE45570A77CE4 (zero_row_id), INDEX IDX_B47CE455F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_historical_date (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_BC32ABF970A77CE4 (zero_row_id), INDEX IDX_BC32ABF9F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_material (zero_row_id INT NOT NULL, material_id INT NOT NULL, INDEX IDX_2DB9C58F70A77CE4 (zero_row_id), INDEX IDX_2DB9C58FE308AC6F (material_id), PRIMARY KEY(zero_row_id, material_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_materials (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_6624E87F70A77CE4 (zero_row_id), INDEX IDX_6624E87FF60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bibliography__authorship ADD CONSTRAINT FK_4CDFAD337ACA5D3F FOREIGN KEY (bibliographic_record_id) REFERENCES bibliography__record (id)');
        $this->addSql('ALTER TABLE bibliography__authorship ADD CONSTRAINT FK_4CDFAD33F675F31B FOREIGN KEY (author_id) REFERENCES bibliography__author (id)');
        $this->addSql('ALTER TABLE bibliography__references_list__item ADD CONSTRAINT FK_E0644C53B8550050 FOREIGN KEY (bibliographic_record_list_id) REFERENCES bibliography__references_list__references_list (id)');
        $this->addSql('ALTER TABLE bibliography__references_list__item ADD CONSTRAINT FK_E0644C537ACA5D3F FOREIGN KEY (bibliographic_record_id) REFERENCES bibliography__record (id)');
        $this->addSql('ALTER TABLE carrier ADD CONSTRAINT FK_4739F11CC54C8C93 FOREIGN KEY (type_id) REFERENCES carrier_type (id)');
        $this->addSql('ALTER TABLE carrier ADD CONSTRAINT FK_4739F11C12469DE2 FOREIGN KEY (category_id) REFERENCES carrier_category (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D621DFC797 FOREIGN KEY (carrier_id) REFERENCES carrier (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D670A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id)');
        $this->addSql('ALTER TABLE interpretation ADD CONSTRAINT FK_EBDBD117E7360910 FOREIGN KEY (writing_type_id) REFERENCES writing_type (id)');
        $this->addSql('ALTER TABLE interpretation ADD CONSTRAINT FK_EBDBD11794F4A73A FOREIGN KEY (writing_method_id) REFERENCES writing_method (id)');
        $this->addSql('ALTER TABLE interpretation ADD CONSTRAINT FK_EBDBD117FE71FA16 FOREIGN KEY (preservation_state_id) REFERENCES preservation_state (id)');
        $this->addSql('ALTER TABLE interpretation ADD CONSTRAINT FK_EBDBD11786D95EE5 FOREIGN KEY (alphabet_id) REFERENCES alphabet (id)');
        $this->addSql('ALTER TABLE interpretation ADD CONSTRAINT FK_EBDBD117416C3764 FOREIGN KEY (content_category_id) REFERENCES content_category (id)');
        $this->addSql('ALTER TABLE interpretation ADD CONSTRAINT FK_EBDBD1175DAC5993 FOREIGN KEY (inscription_id) REFERENCES inscription (id)');
        $this->addSql('ALTER TABLE interpretation_material ADD CONSTRAINT FK_852FD531F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interpretation_material ADD CONSTRAINT FK_852FD531E308AC6F FOREIGN KEY (material_id) REFERENCES material (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row ADD CONSTRAINT FK_34AB4D18E7360910 FOREIGN KEY (writing_type_id) REFERENCES writing_type (id)');
        $this->addSql('ALTER TABLE zero_row ADD CONSTRAINT FK_34AB4D1894F4A73A FOREIGN KEY (writing_method_id) REFERENCES writing_method (id)');
        $this->addSql('ALTER TABLE zero_row ADD CONSTRAINT FK_34AB4D18FE71FA16 FOREIGN KEY (preservation_state_id) REFERENCES preservation_state (id)');
        $this->addSql('ALTER TABLE zero_row ADD CONSTRAINT FK_34AB4D1886D95EE5 FOREIGN KEY (alphabet_id) REFERENCES alphabet (id)');
        $this->addSql('ALTER TABLE zero_row ADD CONSTRAINT FK_34AB4D18416C3764 FOREIGN KEY (content_category_id) REFERENCES content_category (id)');
        $this->addSql('ALTER TABLE zero_row_place_on_carrier ADD CONSTRAINT FK_FA2C803D70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_place_on_carrier ADD CONSTRAINT FK_FA2C803DF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_writing_type ADD CONSTRAINT FK_B22625EE70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_writing_type ADD CONSTRAINT FK_B22625EEF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_writing_method ADD CONSTRAINT FK_64E9550170A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_writing_method ADD CONSTRAINT FK_64E95501F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_preservation_state ADD CONSTRAINT FK_70FF924270A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_preservation_state ADD CONSTRAINT FK_70FF9242F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_alphabet ADD CONSTRAINT FK_41397D6570A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_alphabet ADD CONSTRAINT FK_41397D65F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_text ADD CONSTRAINT FK_3147CA0970A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_text ADD CONSTRAINT FK_3147CA09F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_text_image_file_names ADD CONSTRAINT FK_1D0A6FAF70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_text_image_file_names ADD CONSTRAINT FK_1D0A6FAFF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_transliteration ADD CONSTRAINT FK_FEC2069070A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_transliteration ADD CONSTRAINT FK_FEC20690F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_translation ADD CONSTRAINT FK_DF79588370A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_translation ADD CONSTRAINT FK_DF795883F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_photo_file_names ADD CONSTRAINT FK_C1ACA3FF70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_photo_file_names ADD CONSTRAINT FK_C1ACA3FFF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_sketch_file_names ADD CONSTRAINT FK_3F57EA5770A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_sketch_file_names ADD CONSTRAINT FK_3F57EA57F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_content_category ADD CONSTRAINT FK_818347AE70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_content_category ADD CONSTRAINT FK_818347AEF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_content ADD CONSTRAINT FK_2775109870A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_content ADD CONSTRAINT FK_27751098F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_date_in_text ADD CONSTRAINT FK_8100211070A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_date_in_text ADD CONSTRAINT FK_81002110F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_stratigraphical_date ADD CONSTRAINT FK_B63AA8F070A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_stratigraphical_date ADD CONSTRAINT FK_B63AA8F0F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_non_stratigraphical_date ADD CONSTRAINT FK_B47CE45570A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_non_stratigraphical_date ADD CONSTRAINT FK_B47CE455F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_historical_date ADD CONSTRAINT FK_BC32ABF970A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_historical_date ADD CONSTRAINT FK_BC32ABF9F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_material ADD CONSTRAINT FK_2DB9C58F70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_material ADD CONSTRAINT FK_2DB9C58FE308AC6F FOREIGN KEY (material_id) REFERENCES material (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_materials ADD CONSTRAINT FK_6624E87F70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_materials ADD CONSTRAINT FK_6624E87FF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interpretation DROP FOREIGN KEY FK_EBDBD11786D95EE5');
        $this->addSql('ALTER TABLE zero_row DROP FOREIGN KEY FK_34AB4D1886D95EE5');
        $this->addSql('ALTER TABLE bibliography__authorship DROP FOREIGN KEY FK_4CDFAD33F675F31B');
        $this->addSql('ALTER TABLE bibliography__authorship DROP FOREIGN KEY FK_4CDFAD337ACA5D3F');
        $this->addSql('ALTER TABLE bibliography__references_list__item DROP FOREIGN KEY FK_E0644C537ACA5D3F');
        $this->addSql('ALTER TABLE bibliography__references_list__item DROP FOREIGN KEY FK_E0644C53B8550050');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D621DFC797');
        $this->addSql('ALTER TABLE carrier DROP FOREIGN KEY FK_4739F11C12469DE2');
        $this->addSql('ALTER TABLE carrier DROP FOREIGN KEY FK_4739F11CC54C8C93');
        $this->addSql('ALTER TABLE interpretation DROP FOREIGN KEY FK_EBDBD117416C3764');
        $this->addSql('ALTER TABLE zero_row DROP FOREIGN KEY FK_34AB4D18416C3764');
        $this->addSql('ALTER TABLE interpretation DROP FOREIGN KEY FK_EBDBD1175DAC5993');
        $this->addSql('ALTER TABLE interpretation_material DROP FOREIGN KEY FK_852FD531F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_place_on_carrier DROP FOREIGN KEY FK_FA2C803DF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_writing_type DROP FOREIGN KEY FK_B22625EEF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_writing_method DROP FOREIGN KEY FK_64E95501F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_preservation_state DROP FOREIGN KEY FK_70FF9242F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_alphabet DROP FOREIGN KEY FK_41397D65F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_text DROP FOREIGN KEY FK_3147CA09F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_text_image_file_names DROP FOREIGN KEY FK_1D0A6FAFF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_transliteration DROP FOREIGN KEY FK_FEC20690F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_translation DROP FOREIGN KEY FK_DF795883F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_photo_file_names DROP FOREIGN KEY FK_C1ACA3FFF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_sketch_file_names DROP FOREIGN KEY FK_3F57EA57F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_content_category DROP FOREIGN KEY FK_818347AEF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_content DROP FOREIGN KEY FK_27751098F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_date_in_text DROP FOREIGN KEY FK_81002110F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_stratigraphical_date DROP FOREIGN KEY FK_B63AA8F0F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_non_stratigraphical_date DROP FOREIGN KEY FK_B47CE455F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_historical_date DROP FOREIGN KEY FK_BC32ABF9F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_materials DROP FOREIGN KEY FK_6624E87FF60A8F2C');
        $this->addSql('ALTER TABLE interpretation_material DROP FOREIGN KEY FK_852FD531E308AC6F');
        $this->addSql('ALTER TABLE zero_row_material DROP FOREIGN KEY FK_2DB9C58FE308AC6F');
        $this->addSql('ALTER TABLE interpretation DROP FOREIGN KEY FK_EBDBD117FE71FA16');
        $this->addSql('ALTER TABLE zero_row DROP FOREIGN KEY FK_34AB4D18FE71FA16');
        $this->addSql('ALTER TABLE interpretation DROP FOREIGN KEY FK_EBDBD11794F4A73A');
        $this->addSql('ALTER TABLE zero_row DROP FOREIGN KEY FK_34AB4D1894F4A73A');
        $this->addSql('ALTER TABLE interpretation DROP FOREIGN KEY FK_EBDBD117E7360910');
        $this->addSql('ALTER TABLE zero_row DROP FOREIGN KEY FK_34AB4D18E7360910');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D670A77CE4');
        $this->addSql('ALTER TABLE zero_row_place_on_carrier DROP FOREIGN KEY FK_FA2C803D70A77CE4');
        $this->addSql('ALTER TABLE zero_row_writing_type DROP FOREIGN KEY FK_B22625EE70A77CE4');
        $this->addSql('ALTER TABLE zero_row_writing_method DROP FOREIGN KEY FK_64E9550170A77CE4');
        $this->addSql('ALTER TABLE zero_row_preservation_state DROP FOREIGN KEY FK_70FF924270A77CE4');
        $this->addSql('ALTER TABLE zero_row_alphabet DROP FOREIGN KEY FK_41397D6570A77CE4');
        $this->addSql('ALTER TABLE zero_row_text DROP FOREIGN KEY FK_3147CA0970A77CE4');
        $this->addSql('ALTER TABLE zero_row_text_image_file_names DROP FOREIGN KEY FK_1D0A6FAF70A77CE4');
        $this->addSql('ALTER TABLE zero_row_transliteration DROP FOREIGN KEY FK_FEC2069070A77CE4');
        $this->addSql('ALTER TABLE zero_row_translation DROP FOREIGN KEY FK_DF79588370A77CE4');
        $this->addSql('ALTER TABLE zero_row_photo_file_names DROP FOREIGN KEY FK_C1ACA3FF70A77CE4');
        $this->addSql('ALTER TABLE zero_row_sketch_file_names DROP FOREIGN KEY FK_3F57EA5770A77CE4');
        $this->addSql('ALTER TABLE zero_row_content_category DROP FOREIGN KEY FK_818347AE70A77CE4');
        $this->addSql('ALTER TABLE zero_row_content DROP FOREIGN KEY FK_2775109870A77CE4');
        $this->addSql('ALTER TABLE zero_row_date_in_text DROP FOREIGN KEY FK_8100211070A77CE4');
        $this->addSql('ALTER TABLE zero_row_stratigraphical_date DROP FOREIGN KEY FK_B63AA8F070A77CE4');
        $this->addSql('ALTER TABLE zero_row_non_stratigraphical_date DROP FOREIGN KEY FK_B47CE45570A77CE4');
        $this->addSql('ALTER TABLE zero_row_historical_date DROP FOREIGN KEY FK_BC32ABF970A77CE4');
        $this->addSql('ALTER TABLE zero_row_material DROP FOREIGN KEY FK_2DB9C58F70A77CE4');
        $this->addSql('ALTER TABLE zero_row_materials DROP FOREIGN KEY FK_6624E87F70A77CE4');
        $this->addSql('DROP TABLE alphabet');
        $this->addSql('DROP TABLE bibliography__author');
        $this->addSql('DROP TABLE bibliography__authorship');
        $this->addSql('DROP TABLE bibliography__record');
        $this->addSql('DROP TABLE bibliography__references_list__item');
        $this->addSql('DROP TABLE bibliography__references_list__references_list');
        $this->addSql('DROP TABLE carrier');
        $this->addSql('DROP TABLE carrier_category');
        $this->addSql('DROP TABLE carrier_type');
        $this->addSql('DROP TABLE content_category');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE interpretation');
        $this->addSql('DROP TABLE interpretation_material');
        $this->addSql('DROP TABLE material');
        $this->addSql('DROP TABLE preservation_state');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE writing_method');
        $this->addSql('DROP TABLE writing_type');
        $this->addSql('DROP TABLE zero_row');
        $this->addSql('DROP TABLE zero_row_place_on_carrier');
        $this->addSql('DROP TABLE zero_row_writing_type');
        $this->addSql('DROP TABLE zero_row_writing_method');
        $this->addSql('DROP TABLE zero_row_preservation_state');
        $this->addSql('DROP TABLE zero_row_alphabet');
        $this->addSql('DROP TABLE zero_row_text');
        $this->addSql('DROP TABLE zero_row_text_image_file_names');
        $this->addSql('DROP TABLE zero_row_transliteration');
        $this->addSql('DROP TABLE zero_row_translation');
        $this->addSql('DROP TABLE zero_row_photo_file_names');
        $this->addSql('DROP TABLE zero_row_sketch_file_names');
        $this->addSql('DROP TABLE zero_row_content_category');
        $this->addSql('DROP TABLE zero_row_content');
        $this->addSql('DROP TABLE zero_row_date_in_text');
        $this->addSql('DROP TABLE zero_row_stratigraphical_date');
        $this->addSql('DROP TABLE zero_row_non_stratigraphical_date');
        $this->addSql('DROP TABLE zero_row_historical_date');
        $this->addSql('DROP TABLE zero_row_material');
        $this->addSql('DROP TABLE zero_row_materials');
    }
}
