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
final class Version20201114165057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Changed all one-to-many relationships between ZeroRow/Interpretation and named entities to many-to-many.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE zero_row_materials TO zero_row_material_references');
        $this->addSql('ALTER TABLE zero_row_material_references DROP FOREIGN KEY FK_6624E87F70A77CE4');
        $this->addSql('ALTER TABLE zero_row_material_references DROP FOREIGN KEY FK_6624E87FF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_material_references ADD CONSTRAINT FK_6ED81970A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_material_references ADD CONSTRAINT FK_6ED819F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_place_on_carrier TO zero_row_place_on_carrier_references');
        $this->addSql('ALTER TABLE zero_row_place_on_carrier_references DROP FOREIGN KEY FK_FA2C803D70A77CE4');
        $this->addSql('ALTER TABLE zero_row_place_on_carrier_references DROP FOREIGN KEY FK_FA2C803DF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_place_on_carrier_references ADD CONSTRAINT FK_F3C0ABCB70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_place_on_carrier_references ADD CONSTRAINT FK_F3C0ABCBF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_writing_type TO zero_row_writing_type_references');
        $this->addSql('ALTER TABLE zero_row_writing_type_references DROP FOREIGN KEY FK_B22625EE70A77CE4');
        $this->addSql('ALTER TABLE zero_row_writing_type_references DROP FOREIGN KEY FK_B22625EEF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_writing_type_references ADD CONSTRAINT FK_455AB72270A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_writing_type_references ADD CONSTRAINT FK_455AB722F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_writing_method TO zero_row_writing_method_references');
        $this->addSql('ALTER TABLE zero_row_writing_method_references DROP FOREIGN KEY FK_64E9550170A77CE4');
        $this->addSql('ALTER TABLE zero_row_writing_method_references DROP FOREIGN KEY FK_64E95501F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_writing_method_references ADD CONSTRAINT FK_A0D2D6D970A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_writing_method_references ADD CONSTRAINT FK_A0D2D6D9F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_preservation_state TO zero_row_preservation_state_references');
        $this->addSql('ALTER TABLE zero_row_preservation_state_references DROP FOREIGN KEY FK_70FF924270A77CE4');
        $this->addSql('ALTER TABLE zero_row_preservation_state_references DROP FOREIGN KEY FK_70FF9242F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_preservation_state_references ADD CONSTRAINT FK_5230B8C570A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_preservation_state_references ADD CONSTRAINT FK_5230B8C5F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_alphabet TO zero_row_alphabet_references');
        $this->addSql('ALTER TABLE zero_row_alphabet_references DROP FOREIGN KEY FK_41397D6570A77CE4');
        $this->addSql('ALTER TABLE zero_row_alphabet_references DROP FOREIGN KEY FK_41397D65F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_alphabet_references ADD CONSTRAINT FK_B9C45D4C70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_alphabet_references ADD CONSTRAINT FK_B9C45D4CF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_text TO zero_row_text_references');
        $this->addSql('ALTER TABLE zero_row_text_references DROP FOREIGN KEY FK_3147CA0970A77CE4');
        $this->addSql('ALTER TABLE zero_row_text_references DROP FOREIGN KEY FK_3147CA09F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_text_references ADD CONSTRAINT FK_1B11402470A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_text_references ADD CONSTRAINT FK_1B114024F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_text_image_file_names TO zero_row_text_image_file_names_references');
        $this->addSql('ALTER TABLE zero_row_text_image_file_names_references DROP FOREIGN KEY FK_1D0A6FAF70A77CE4');
        $this->addSql('ALTER TABLE zero_row_text_image_file_names_references DROP FOREIGN KEY FK_1D0A6FAFF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_text_image_file_names_references ADD CONSTRAINT FK_CCAC62F370A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_text_image_file_names_references ADD CONSTRAINT FK_CCAC62F3F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_transliteration TO zero_row_transliteration_references');
        $this->addSql('ALTER TABLE zero_row_transliteration_references DROP FOREIGN KEY FK_FEC2069070A77CE4');
        $this->addSql('ALTER TABLE zero_row_transliteration_references DROP FOREIGN KEY FK_FEC20690F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_transliteration_references ADD CONSTRAINT FK_548C714170A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_transliteration_references ADD CONSTRAINT FK_548C7141F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_translation TO zero_row_translation_references');
        $this->addSql('ALTER TABLE zero_row_translation_references DROP FOREIGN KEY FK_DF79588370A77CE4');
        $this->addSql('ALTER TABLE zero_row_translation_references DROP FOREIGN KEY FK_DF795883F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_translation_references ADD CONSTRAINT FK_3BA859F870A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_translation_references ADD CONSTRAINT FK_3BA859F8F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_photo_file_names TO zero_row_photo_file_names_references');
        $this->addSql('ALTER TABLE zero_row_photo_file_names_references DROP FOREIGN KEY FK_C1ACA3FF70A77CE4');
        $this->addSql('ALTER TABLE zero_row_photo_file_names_references DROP FOREIGN KEY FK_C1ACA3FFF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_photo_file_names_references ADD CONSTRAINT FK_109DC9CD70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_photo_file_names_references ADD CONSTRAINT FK_109DC9CDF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_sketch_file_names TO zero_row_sketch_file_names_references');
        $this->addSql('ALTER TABLE zero_row_sketch_file_names_references DROP FOREIGN KEY FK_3F57EA5770A77CE4');
        $this->addSql('ALTER TABLE zero_row_sketch_file_names_references DROP FOREIGN KEY FK_3F57EA57F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_sketch_file_names_references ADD CONSTRAINT FK_C7CEE59570A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_sketch_file_names_references ADD CONSTRAINT FK_C7CEE595F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_content_category TO zero_row_content_category_references');
        $this->addSql('ALTER TABLE zero_row_content_category_references DROP FOREIGN KEY FK_818347AE70A77CE4');
        $this->addSql('ALTER TABLE zero_row_content_category_references DROP FOREIGN KEY FK_818347AEF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_content_category_references ADD CONSTRAINT FK_E371E9BD70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_content_category_references ADD CONSTRAINT FK_E371E9BDF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_content TO zero_row_content_references');
        $this->addSql('ALTER TABLE zero_row_content_references DROP FOREIGN KEY FK_2775109870A77CE4');
        $this->addSql('ALTER TABLE zero_row_content_references DROP FOREIGN KEY FK_27751098F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_content_references ADD CONSTRAINT FK_F1A8A1C870A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_content_references ADD CONSTRAINT FK_F1A8A1C8F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_date_in_text TO zero_row_date_in_text_references');
        $this->addSql('ALTER TABLE zero_row_date_in_text_references DROP FOREIGN KEY FK_8100211070A77CE4');
        $this->addSql('ALTER TABLE zero_row_date_in_text_references DROP FOREIGN KEY FK_81002110F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_date_in_text_references ADD CONSTRAINT FK_6DADF48570A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_date_in_text_references ADD CONSTRAINT FK_6DADF485F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_stratigraphical_date TO zero_row_stratigraphical_date_references');
        $this->addSql('ALTER TABLE zero_row_stratigraphical_date_references DROP FOREIGN KEY FK_B63AA8F070A77CE4');
        $this->addSql('ALTER TABLE zero_row_stratigraphical_date_references DROP FOREIGN KEY FK_B63AA8F0F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_stratigraphical_date_references ADD CONSTRAINT FK_649F65C170A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_stratigraphical_date_references ADD CONSTRAINT FK_649F65C1F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_non_stratigraphical_date TO zero_row_non_stratigraphical_date_references');
        $this->addSql('ALTER TABLE zero_row_non_stratigraphical_date_references DROP FOREIGN KEY FK_B47CE45570A77CE4');
        $this->addSql('ALTER TABLE zero_row_non_stratigraphical_date_references DROP FOREIGN KEY FK_B47CE455F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_non_stratigraphical_date_references ADD CONSTRAINT FK_8F9EE91570A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_non_stratigraphical_date_references ADD CONSTRAINT FK_8F9EE915F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('RENAME TABLE zero_row_historical_date TO zero_row_historical_date_references');
        $this->addSql('ALTER TABLE zero_row_historical_date_references DROP FOREIGN KEY FK_BC32ABF970A77CE4');
        $this->addSql('ALTER TABLE zero_row_historical_date_references DROP FOREIGN KEY FK_BC32ABF9F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_historical_date_references ADD CONSTRAINT FK_DEFD5E9770A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_historical_date_references ADD CONSTRAINT FK_DEFD5E97F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE zero_row_place_on_carrier_references RENAME INDEX idx_fa2c803d70a77ce4 TO IDX_F3C0ABCB70A77CE4');
        $this->addSql('ALTER TABLE zero_row_place_on_carrier_references RENAME INDEX idx_fa2c803df60a8f2c TO IDX_F3C0ABCBF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_writing_type_references RENAME INDEX idx_b22625ee70a77ce4 TO IDX_455AB72270A77CE4');
        $this->addSql('ALTER TABLE zero_row_writing_type_references RENAME INDEX idx_b22625eef60a8f2c TO IDX_455AB722F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_writing_method_references RENAME INDEX idx_64e9550170a77ce4 TO IDX_A0D2D6D970A77CE4');
        $this->addSql('ALTER TABLE zero_row_writing_method_references RENAME INDEX idx_64e95501f60a8f2c TO IDX_A0D2D6D9F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_preservation_state_references RENAME INDEX idx_70ff924270a77ce4 TO IDX_5230B8C570A77CE4');
        $this->addSql('ALTER TABLE zero_row_preservation_state_references RENAME INDEX idx_70ff9242f60a8f2c TO IDX_5230B8C5F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_text_references RENAME INDEX idx_3147ca0970a77ce4 TO IDX_1B11402470A77CE4');
        $this->addSql('ALTER TABLE zero_row_text_references RENAME INDEX idx_3147ca09f60a8f2c TO IDX_1B114024F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_text_image_file_names_references RENAME INDEX idx_1d0a6faf70a77ce4 TO IDX_CCAC62F370A77CE4');
        $this->addSql('ALTER TABLE zero_row_text_image_file_names_references RENAME INDEX idx_1d0a6faff60a8f2c TO IDX_CCAC62F3F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_transliteration_references RENAME INDEX idx_fec2069070a77ce4 TO IDX_548C714170A77CE4');
        $this->addSql('ALTER TABLE zero_row_transliteration_references RENAME INDEX idx_fec20690f60a8f2c TO IDX_548C7141F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_translation_references RENAME INDEX idx_df79588370a77ce4 TO IDX_3BA859F870A77CE4');
        $this->addSql('ALTER TABLE zero_row_translation_references RENAME INDEX idx_df795883f60a8f2c TO IDX_3BA859F8F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_photo_file_names_references RENAME INDEX idx_c1aca3ff70a77ce4 TO IDX_109DC9CD70A77CE4');
        $this->addSql('ALTER TABLE zero_row_photo_file_names_references RENAME INDEX idx_c1aca3fff60a8f2c TO IDX_109DC9CDF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_sketch_file_names_references RENAME INDEX idx_3f57ea5770a77ce4 TO IDX_C7CEE59570A77CE4');
        $this->addSql('ALTER TABLE zero_row_sketch_file_names_references RENAME INDEX idx_3f57ea57f60a8f2c TO IDX_C7CEE595F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_content_category_references RENAME INDEX idx_818347ae70a77ce4 TO IDX_E371E9BD70A77CE4');
        $this->addSql('ALTER TABLE zero_row_content_category_references RENAME INDEX idx_818347aef60a8f2c TO IDX_E371E9BDF60A8F2C');
        $this->addSql('ALTER TABLE zero_row_content_references RENAME INDEX idx_2775109870a77ce4 TO IDX_F1A8A1C870A77CE4');
        $this->addSql('ALTER TABLE zero_row_content_references RENAME INDEX idx_27751098f60a8f2c TO IDX_F1A8A1C8F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_date_in_text_references RENAME INDEX idx_8100211070a77ce4 TO IDX_6DADF48570A77CE4');
        $this->addSql('ALTER TABLE zero_row_date_in_text_references RENAME INDEX idx_81002110f60a8f2c TO IDX_6DADF485F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_stratigraphical_date_references RENAME INDEX idx_b63aa8f070a77ce4 TO IDX_649F65C170A77CE4');
        $this->addSql('ALTER TABLE zero_row_stratigraphical_date_references RENAME INDEX idx_b63aa8f0f60a8f2c TO IDX_649F65C1F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_non_stratigraphical_date_references RENAME INDEX idx_b47ce45570a77ce4 TO IDX_8F9EE91570A77CE4');
        $this->addSql('ALTER TABLE zero_row_non_stratigraphical_date_references RENAME INDEX idx_b47ce455f60a8f2c TO IDX_8F9EE915F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_historical_date_references RENAME INDEX idx_bc32abf970a77ce4 TO IDX_DEFD5E9770A77CE4');
        $this->addSql('ALTER TABLE zero_row_historical_date_references RENAME INDEX idx_bc32abf9f60a8f2c TO IDX_DEFD5E97F60A8F2C');
        $this->addSql('ALTER TABLE zero_row_material_references RENAME INDEX idx_6624e87f70a77ce4 TO IDX_6ED81970A77CE4');
        $this->addSql('ALTER TABLE zero_row_material_references RENAME INDEX idx_6624e87ff60a8f2c TO IDX_6ED819F60A8F2C');

        $this->addSql('CREATE TABLE interpretation_content_category (interpretation_id INT NOT NULL, content_category_id INT NOT NULL, INDEX IDX_AE493EFCF60A8F2C (interpretation_id), INDEX IDX_AE493EFC416C3764 (content_category_id), PRIMARY KEY(interpretation_id, content_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_content_category (zero_row_id INT NOT NULL, content_category_id INT NOT NULL, INDEX IDX_818347AE70A77CE4 (zero_row_id), INDEX IDX_818347AE416C3764 (content_category_id), PRIMARY KEY(zero_row_id, content_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interpretation_content_category ADD CONSTRAINT FK_AE493EFCF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interpretation_content_category ADD CONSTRAINT FK_AE493EFC416C3764 FOREIGN KEY (content_category_id) REFERENCES content_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_content_category ADD CONSTRAINT FK_818347AE70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_content_category ADD CONSTRAINT FK_818347AE416C3764 FOREIGN KEY (content_category_id) REFERENCES content_category (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO interpretation_content_category (interpretation_id, content_category_id) SELECT id, content_category_id FROM interpretation WHERE content_category_id IS NOT NULL');
        $this->addSql('INSERT INTO zero_row_content_category (zero_row_id, content_category_id) SELECT id, content_category_id FROM zero_row WHERE content_category_id IS NOT NULL');
        $this->addSql('ALTER TABLE interpretation DROP FOREIGN KEY FK_EBDBD117416C3764');
        $this->addSql('DROP INDEX IDX_EBDBD117416C3764 ON interpretation');
        $this->addSql('ALTER TABLE interpretation DROP content_category_id');
        $this->addSql('ALTER TABLE zero_row DROP FOREIGN KEY FK_34AB4D18416C3764');
        $this->addSql('DROP INDEX IDX_34AB4D18416C3764 ON zero_row');
        $this->addSql('ALTER TABLE zero_row DROP content_category_id');

        $this->addSql('CREATE TABLE interpretation_alphabet (interpretation_id INT NOT NULL, alphabet_id INT NOT NULL, INDEX IDX_E9AF6DDBF60A8F2C (interpretation_id), INDEX IDX_E9AF6DDB86D95EE5 (alphabet_id), PRIMARY KEY(interpretation_id, alphabet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_alphabet (zero_row_id INT NOT NULL, alphabet_id INT NOT NULL, INDEX IDX_41397D6570A77CE4 (zero_row_id), INDEX IDX_41397D6586D95EE5 (alphabet_id), PRIMARY KEY(zero_row_id, alphabet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interpretation_alphabet ADD CONSTRAINT FK_E9AF6DDBF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interpretation_alphabet ADD CONSTRAINT FK_E9AF6DDB86D95EE5 FOREIGN KEY (alphabet_id) REFERENCES alphabet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_alphabet ADD CONSTRAINT FK_41397D6570A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_alphabet ADD CONSTRAINT FK_41397D6586D95EE5 FOREIGN KEY (alphabet_id) REFERENCES alphabet (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO interpretation_alphabet (interpretation_id, alphabet_id) SELECT id, alphabet_id FROM interpretation WHERE alphabet_id IS NOT NULL');
        $this->addSql('INSERT INTO zero_row_alphabet (zero_row_id, alphabet_id) SELECT id, alphabet_id FROM zero_row WHERE alphabet_id IS NOT NULL');
        $this->addSql('ALTER TABLE interpretation DROP FOREIGN KEY FK_EBDBD11786D95EE5');
        $this->addSql('DROP INDEX IDX_EBDBD11786D95EE5 ON interpretation');
        $this->addSql('ALTER TABLE interpretation DROP alphabet_id');
        $this->addSql('ALTER TABLE zero_row DROP FOREIGN KEY FK_34AB4D1886D95EE5');
        $this->addSql('DROP INDEX IDX_34AB4D1886D95EE5 ON zero_row');
        $this->addSql('ALTER TABLE zero_row DROP alphabet_id');
        $this->addSql('ALTER TABLE zero_row_alphabet_references RENAME INDEX idx_41397d6570a77ce4 TO IDX_B9C45D4C70A77CE4');
        $this->addSql('ALTER TABLE zero_row_alphabet_references RENAME INDEX idx_41397d65f60a8f2c TO IDX_B9C45D4CF60A8F2C');

        $this->addSql('CREATE TABLE interpretation_preservation_state (interpretation_id INT NOT NULL, preservation_state_id INT NOT NULL, INDEX IDX_D1AB4BC6F60A8F2C (interpretation_id), INDEX IDX_D1AB4BC6FE71FA16 (preservation_state_id), PRIMARY KEY(interpretation_id, preservation_state_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_preservation_state (zero_row_id INT NOT NULL, preservation_state_id INT NOT NULL, INDEX IDX_70FF924270A77CE4 (zero_row_id), INDEX IDX_70FF9242FE71FA16 (preservation_state_id), PRIMARY KEY(zero_row_id, preservation_state_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interpretation_preservation_state ADD CONSTRAINT FK_D1AB4BC6F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interpretation_preservation_state ADD CONSTRAINT FK_D1AB4BC6FE71FA16 FOREIGN KEY (preservation_state_id) REFERENCES preservation_state (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_preservation_state ADD CONSTRAINT FK_70FF924270A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_preservation_state ADD CONSTRAINT FK_70FF9242FE71FA16 FOREIGN KEY (preservation_state_id) REFERENCES preservation_state (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO interpretation_preservation_state (interpretation_id, preservation_state_id) SELECT id, preservation_state_id FROM interpretation WHERE preservation_state_id IS NOT NULL');
        $this->addSql('INSERT INTO zero_row_preservation_state (zero_row_id, preservation_state_id) SELECT id, preservation_state_id FROM zero_row WHERE preservation_state_id IS NOT NULL');
        $this->addSql('ALTER TABLE interpretation DROP FOREIGN KEY FK_EBDBD117FE71FA16');
        $this->addSql('DROP INDEX IDX_EBDBD117FE71FA16 ON interpretation');
        $this->addSql('ALTER TABLE interpretation DROP preservation_state_id');
        $this->addSql('ALTER TABLE zero_row DROP FOREIGN KEY FK_34AB4D18FE71FA16');
        $this->addSql('DROP INDEX IDX_34AB4D18FE71FA16 ON zero_row');
        $this->addSql('ALTER TABLE zero_row DROP preservation_state_id');

        $this->addSql('CREATE TABLE interpretation_writing_method (interpretation_id INT NOT NULL, writing_method_id INT NOT NULL, INDEX IDX_6B890226F60A8F2C (interpretation_id), INDEX IDX_6B89022694F4A73A (writing_method_id), PRIMARY KEY(interpretation_id, writing_method_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_writing_method (zero_row_id INT NOT NULL, writing_method_id INT NOT NULL, INDEX IDX_64E9550170A77CE4 (zero_row_id), INDEX IDX_64E9550194F4A73A (writing_method_id), PRIMARY KEY(zero_row_id, writing_method_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interpretation_writing_method ADD CONSTRAINT FK_6B890226F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interpretation_writing_method ADD CONSTRAINT FK_6B89022694F4A73A FOREIGN KEY (writing_method_id) REFERENCES writing_method (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_writing_method ADD CONSTRAINT FK_64E9550170A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_writing_method ADD CONSTRAINT FK_64E9550194F4A73A FOREIGN KEY (writing_method_id) REFERENCES writing_method (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO interpretation_writing_method (interpretation_id, writing_method_id) SELECT id, writing_method_id FROM interpretation WHERE writing_method_id IS NOT NULL');
        $this->addSql('INSERT INTO zero_row_writing_method (zero_row_id, writing_method_id) SELECT id, writing_method_id FROM zero_row WHERE writing_method_id IS NOT NULL');
        $this->addSql('ALTER TABLE interpretation DROP FOREIGN KEY FK_EBDBD11794F4A73A');
        $this->addSql('DROP INDEX IDX_EBDBD11794F4A73A ON interpretation');
        $this->addSql('ALTER TABLE interpretation DROP writing_method_id');
        $this->addSql('ALTER TABLE zero_row DROP FOREIGN KEY FK_34AB4D1894F4A73A');
        $this->addSql('DROP INDEX IDX_34AB4D1894F4A73A ON zero_row');
        $this->addSql('ALTER TABLE zero_row DROP writing_method_id');

        $this->addSql('CREATE TABLE interpretation_writing_type (interpretation_id INT NOT NULL, writing_type_id INT NOT NULL, INDEX IDX_ACBBB971F60A8F2C (interpretation_id), INDEX IDX_ACBBB971E7360910 (writing_type_id), PRIMARY KEY(interpretation_id, writing_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_writing_type (zero_row_id INT NOT NULL, writing_type_id INT NOT NULL, INDEX IDX_B22625EE70A77CE4 (zero_row_id), INDEX IDX_B22625EEE7360910 (writing_type_id), PRIMARY KEY(zero_row_id, writing_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interpretation_writing_type ADD CONSTRAINT FK_ACBBB971F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interpretation_writing_type ADD CONSTRAINT FK_ACBBB971E7360910 FOREIGN KEY (writing_type_id) REFERENCES writing_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_writing_type ADD CONSTRAINT FK_B22625EE70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_writing_type ADD CONSTRAINT FK_B22625EEE7360910 FOREIGN KEY (writing_type_id) REFERENCES writing_type (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO interpretation_writing_type (interpretation_id, writing_type_id) SELECT id, writing_type_id FROM interpretation WHERE writing_type_id IS NOT NULL');
        $this->addSql('INSERT INTO zero_row_writing_type (zero_row_id, writing_type_id) SELECT id, writing_type_id FROM zero_row WHERE writing_type_id IS NOT NULL');
        $this->addSql('ALTER TABLE interpretation DROP FOREIGN KEY FK_EBDBD117E7360910');
        $this->addSql('DROP INDEX IDX_EBDBD117E7360910 ON interpretation');
        $this->addSql('ALTER TABLE interpretation DROP writing_type_id');
        $this->addSql('ALTER TABLE zero_row DROP FOREIGN KEY FK_34AB4D18E7360910');
        $this->addSql('DROP INDEX IDX_34AB4D18E7360910 ON zero_row');
        $this->addSql('ALTER TABLE zero_row DROP writing_type_id');

        $this->addSql('CREATE TABLE carrier_carrier_type (carrier_id INT NOT NULL, carrier_type_id INT NOT NULL, INDEX IDX_5A3C9D1D21DFC797 (carrier_id), INDEX IDX_5A3C9D1D91BFAADB (carrier_type_id), PRIMARY KEY(carrier_id, carrier_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carrier_carrier_category (carrier_id INT NOT NULL, carrier_category_id INT NOT NULL, INDEX IDX_E6C5832E21DFC797 (carrier_id), INDEX IDX_E6C5832E23B1F0F1 (carrier_category_id), PRIMARY KEY(carrier_id, carrier_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carrier_carrier_type ADD CONSTRAINT FK_5A3C9D1D21DFC797 FOREIGN KEY (carrier_id) REFERENCES carrier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrier_carrier_type ADD CONSTRAINT FK_5A3C9D1D91BFAADB FOREIGN KEY (carrier_type_id) REFERENCES carrier_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrier_carrier_category ADD CONSTRAINT FK_E6C5832E21DFC797 FOREIGN KEY (carrier_id) REFERENCES carrier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrier_carrier_category ADD CONSTRAINT FK_E6C5832E23B1F0F1 FOREIGN KEY (carrier_category_id) REFERENCES carrier_category (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO carrier_carrier_type (carrier_id, carrier_type_id) SELECT id, type_id FROM carrier WHERE type_id IS NOT NULL');
        $this->addSql('INSERT INTO carrier_carrier_category (carrier_id, carrier_category_id) SELECT id, category_id FROM carrier WHERE category_id IS NOT NULL');
        $this->addSql('ALTER TABLE carrier DROP FOREIGN KEY FK_4739F11C12469DE2');
        $this->addSql('ALTER TABLE carrier DROP FOREIGN KEY FK_4739F11CC54C8C93');
        $this->addSql('DROP INDEX IDX_4739F11CC54C8C93 ON carrier');
        $this->addSql('DROP INDEX IDX_4739F11C12469DE2 ON carrier');
        $this->addSql('ALTER TABLE carrier DROP type_id, DROP category_id');
    }
}
