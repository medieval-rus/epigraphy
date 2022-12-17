<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221217123449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carrier_storage_site (carrier_id INT NOT NULL, storage_site_id INT NOT NULL, INDEX IDX_E4E1744221DFC797 (carrier_id), INDEX IDX_E4E1744281812524 (storage_site_id), PRIMARY KEY(carrier_id, storage_site_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carrier_material (carrier_id INT NOT NULL, material_id INT NOT NULL, INDEX IDX_4ED4F1121DFC797 (carrier_id), INDEX IDX_4ED4F11E308AC6F (material_id), PRIMARY KEY(carrier_id, material_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5373C9665E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE river__river_type (river_id INT NOT NULL, river_type_id INT NOT NULL, INDEX IDX_2E7A03F041E62266 (river_id), INDEX IDX_2E7A03F04B0DD784 (river_type_id), PRIMARY KEY(river_id, river_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE river_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3748D9C5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage_site (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, name_aliases LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', comments VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_606EC2F15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage_site_city (storage_site_id INT NOT NULL, city_id INT NOT NULL, INDEX IDX_FEDAA0C281812524 (storage_site_id), INDEX IDX_FEDAA0C28BAC62AF (city_id), PRIMARY KEY(storage_site_id, city_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_reconstruction_references (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_8015421070A77CE4 (zero_row_id), INDEX IDX_80154210F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_normalization_references (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_89F9A42070A77CE4 (zero_row_id), INDEX IDX_89F9A420F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carrier_storage_site ADD CONSTRAINT FK_E4E1744221DFC797 FOREIGN KEY (carrier_id) REFERENCES carrier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrier_storage_site ADD CONSTRAINT FK_E4E1744281812524 FOREIGN KEY (storage_site_id) REFERENCES storage_site (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrier_material ADD CONSTRAINT FK_4ED4F1121DFC797 FOREIGN KEY (carrier_id) REFERENCES carrier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrier_material ADD CONSTRAINT FK_4ED4F11E308AC6F FOREIGN KEY (material_id) REFERENCES material (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE river__river_type ADD CONSTRAINT FK_2E7A03F041E62266 FOREIGN KEY (river_id) REFERENCES river (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE river__river_type ADD CONSTRAINT FK_2E7A03F04B0DD784 FOREIGN KEY (river_type_id) REFERENCES river_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE storage_site_city ADD CONSTRAINT FK_FEDAA0C281812524 FOREIGN KEY (storage_site_id) REFERENCES storage_site (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE storage_site_city ADD CONSTRAINT FK_FEDAA0C28BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_reconstruction_references ADD CONSTRAINT FK_8015421070A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_reconstruction_references ADD CONSTRAINT FK_80154210F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_normalization_references ADD CONSTRAINT FK_89F9A42070A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_normalization_references ADD CONSTRAINT FK_89F9A420F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrier ADD archaeology TEXT DEFAULT NULL, ADD previous_storage TEXT DEFAULT NULL, ADD storage_localization TEXT DEFAULT NULL, ADD material_description TEXT DEFAULT NULL, DROP quadrat, DROP plast_level, DROP yarus_level, DROP depth');
        $this->addSql('ALTER TABLE discovery_site DROP is_outside_river');
        $this->addSql('ALTER TABLE inscription ADD date_explanation TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE interpretation ADD reconstruction TEXT DEFAULT NULL, ADD normalization TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE river DROP type');
        $this->addSql('ALTER TABLE zero_row ADD reconstruction TEXT DEFAULT NULL, ADD normalization TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE river__river_type DROP FOREIGN KEY FK_2E7A03F04B0DD784');
        $this->addSql('ALTER TABLE carrier_storage_site DROP FOREIGN KEY FK_E4E1744281812524');
        $this->addSql('ALTER TABLE storage_site_city DROP FOREIGN KEY FK_FEDAA0C281812524');
        $this->addSql('DROP TABLE carrier_storage_site');
        $this->addSql('DROP TABLE carrier_material');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE river__river_type');
        $this->addSql('DROP TABLE river_type');
        $this->addSql('DROP TABLE storage_site');
        $this->addSql('DROP TABLE storage_site_city');
        $this->addSql('DROP TABLE zero_row_reconstruction_references');
        $this->addSql('DROP TABLE zero_row_normalization_references');
        $this->addSql('ALTER TABLE carrier ADD quadrat INT DEFAULT NULL, ADD plast_level LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', ADD yarus_level LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', ADD depth INT DEFAULT NULL, DROP archaeology, DROP previous_storage, DROP storage_localization, DROP material_description');
        $this->addSql('ALTER TABLE discovery_site ADD is_outside_river TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE inscription DROP date_explanation');
        $this->addSql('ALTER TABLE interpretation DROP reconstruction, DROP normalization');
        $this->addSql('ALTER TABLE river ADD type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE zero_row DROP reconstruction, DROP normalization');
    }
}
