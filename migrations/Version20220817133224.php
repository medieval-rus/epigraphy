<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220817133224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bibliography__references_list CHANGE description description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE carrier ADD supercarrier_id INT DEFAULT NULL, ADD is_super_carrier TINYINT(1) DEFAULT NULL, CHANGE find_circumstances find_circumstances TEXT DEFAULT NULL, CHANGE characteristics characteristics TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE carrier ADD CONSTRAINT FK_4739F11CC0204D55 FOREIGN KEY (supercarrier_id) REFERENCES carrier (id)');
        $this->addSql('CREATE INDEX IDX_4739F11CC0204D55 ON carrier (supercarrier_id)');
        $this->addSql('ALTER TABLE carrier_category ADD supercategory_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE carrier_category ADD CONSTRAINT FK_7E1F234567CDC1F4 FOREIGN KEY (supercategory_id) REFERENCES carrier_category (id)');
        $this->addSql('CREATE INDEX IDX_7E1F234567CDC1F4 ON carrier_category (supercategory_id)');
        $this->addSql('ALTER TABLE content_category ADD supercategory_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE content_category ADD CONSTRAINT FK_54FBF32E67CDC1F4 FOREIGN KEY (supercategory_id) REFERENCES content_category (id)');
        $this->addSql('CREATE INDEX IDX_54FBF32E67CDC1F4 ON content_category (supercategory_id)');
        $this->addSql('ALTER TABLE file CHANGE description description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE inscription CHANGE comment comment TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE interpretation CHANGE place_on_carrier place_on_carrier TEXT DEFAULT NULL, CHANGE text text TEXT DEFAULT NULL, CHANGE transliteration transliteration TEXT DEFAULT NULL, CHANGE translation translation TEXT DEFAULT NULL, CHANGE date_in_text date_in_text TEXT DEFAULT NULL, CHANGE stratigraphical_date stratigraphical_date TEXT DEFAULT NULL, CHANGE non_stratigraphical_date non_stratigraphical_date TEXT DEFAULT NULL, CHANGE historical_date historical_date TEXT DEFAULT NULL, CHANGE comment comment TEXT DEFAULT NULL, CHANGE origin origin TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE material ADD supermaterial_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE material ADD CONSTRAINT FK_7CBE75959683F079 FOREIGN KEY (supermaterial_id) REFERENCES material (id)');
        $this->addSql('CREATE INDEX IDX_7CBE75959683F079 ON material (supermaterial_id)');
        $this->addSql('ALTER TABLE writing_method ADD supermethod_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE writing_method ADD CONSTRAINT FK_26CA00D6E89AE32C FOREIGN KEY (supermethod_id) REFERENCES writing_method (id)');
        $this->addSql('CREATE INDEX IDX_26CA00D6E89AE32C ON writing_method (supermethod_id)');
        $this->addSql('ALTER TABLE zero_row CHANGE place_on_carrier place_on_carrier TEXT DEFAULT NULL, CHANGE text text TEXT DEFAULT NULL, CHANGE transliteration transliteration TEXT DEFAULT NULL, CHANGE translation translation TEXT DEFAULT NULL, CHANGE date_in_text date_in_text TEXT DEFAULT NULL, CHANGE stratigraphical_date stratigraphical_date TEXT DEFAULT NULL, CHANGE non_stratigraphical_date non_stratigraphical_date TEXT DEFAULT NULL, CHANGE historical_date historical_date TEXT DEFAULT NULL, CHANGE origin origin TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bibliography__references_list CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE carrier DROP FOREIGN KEY FK_4739F11CC0204D55');
        $this->addSql('DROP INDEX IDX_4739F11CC0204D55 ON carrier');
        $this->addSql('ALTER TABLE carrier DROP supercarrier_id, DROP is_super_carrier, CHANGE find_circumstances find_circumstances LONGTEXT DEFAULT NULL, CHANGE characteristics characteristics LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE carrier_category DROP FOREIGN KEY FK_7E1F234567CDC1F4');
        $this->addSql('DROP INDEX IDX_7E1F234567CDC1F4 ON carrier_category');
        $this->addSql('ALTER TABLE carrier_category DROP supercategory_id');
        $this->addSql('ALTER TABLE content_category DROP FOREIGN KEY FK_54FBF32E67CDC1F4');
        $this->addSql('DROP INDEX IDX_54FBF32E67CDC1F4 ON content_category');
        $this->addSql('ALTER TABLE content_category DROP supercategory_id');
        $this->addSql('ALTER TABLE file CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE inscription CHANGE comment comment LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE interpretation CHANGE comment comment LONGTEXT DEFAULT NULL, CHANGE origin origin LONGTEXT DEFAULT NULL, CHANGE place_on_carrier place_on_carrier LONGTEXT DEFAULT NULL, CHANGE text text LONGTEXT DEFAULT NULL, CHANGE transliteration transliteration LONGTEXT DEFAULT NULL, CHANGE translation translation LONGTEXT DEFAULT NULL, CHANGE date_in_text date_in_text LONGTEXT DEFAULT NULL, CHANGE stratigraphical_date stratigraphical_date LONGTEXT DEFAULT NULL, CHANGE non_stratigraphical_date non_stratigraphical_date LONGTEXT DEFAULT NULL, CHANGE historical_date historical_date LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE material DROP FOREIGN KEY FK_7CBE75959683F079');
        $this->addSql('DROP INDEX IDX_7CBE75959683F079 ON material');
        $this->addSql('ALTER TABLE material DROP supermaterial_id');
        $this->addSql('ALTER TABLE writing_method DROP FOREIGN KEY FK_26CA00D6E89AE32C');
        $this->addSql('DROP INDEX IDX_26CA00D6E89AE32C ON writing_method');
        $this->addSql('ALTER TABLE writing_method DROP supermethod_id');
        $this->addSql('ALTER TABLE zero_row CHANGE origin origin LONGTEXT DEFAULT NULL, CHANGE place_on_carrier place_on_carrier LONGTEXT DEFAULT NULL, CHANGE text text LONGTEXT DEFAULT NULL, CHANGE transliteration transliteration LONGTEXT DEFAULT NULL, CHANGE translation translation LONGTEXT DEFAULT NULL, CHANGE date_in_text date_in_text LONGTEXT DEFAULT NULL, CHANGE stratigraphical_date stratigraphical_date LONGTEXT DEFAULT NULL, CHANGE non_stratigraphical_date non_stratigraphical_date LONGTEXT DEFAULT NULL, CHANGE historical_date historical_date LONGTEXT DEFAULT NULL');
    }
}
