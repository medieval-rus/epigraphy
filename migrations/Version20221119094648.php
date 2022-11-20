<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221119094648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', type VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, region VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discovery_site (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, name_aliases LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', is_outside_city TINYINT(1) DEFAULT 0 NOT NULL, is_outside_river TINYINT(1) DEFAULT 0 NOT NULL, comments VARCHAR(255) NOT NULL, latitude INT NOT NULL, longitude INT NOT NULL, UNIQUE INDEX UNIQ_C98E52EA5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discovery_site_river (discovery_site_id INT NOT NULL, river_id INT NOT NULL, INDEX IDX_192F433F40B9017E (discovery_site_id), INDEX IDX_192F433F41E62266 (river_id), PRIMARY KEY(discovery_site_id, river_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discovery_site_city (discovery_site_id INT NOT NULL, city_id INT NOT NULL, INDEX IDX_828D30F540B9017E (discovery_site_id), INDEX IDX_828D30F58BAC62AF (city_id), PRIMARY KEY(discovery_site_id, city_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE river (id INT AUTO_INCREMENT NOT NULL, superriver_id INT DEFAULT NULL, name LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', type VARCHAR(255) NOT NULL, INDEX IDX_F5E3672B87DE2BB7 (superriver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE discovery_site_river ADD CONSTRAINT FK_192F433F40B9017E FOREIGN KEY (discovery_site_id) REFERENCES discovery_site (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discovery_site_river ADD CONSTRAINT FK_192F433F41E62266 FOREIGN KEY (river_id) REFERENCES river (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discovery_site_city ADD CONSTRAINT FK_828D30F540B9017E FOREIGN KEY (discovery_site_id) REFERENCES discovery_site (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discovery_site_city ADD CONSTRAINT FK_828D30F58BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE river ADD CONSTRAINT FK_F5E3672B87DE2BB7 FOREIGN KEY (superriver_id) REFERENCES river (id)');
        $this->addSql('ALTER TABLE carrier ADD carrier_history TEXT DEFAULT NULL, ADD quadrat INT DEFAULT NULL, ADD plast_level LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', ADD yarus_level LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', ADD depth INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discovery_site_city DROP FOREIGN KEY FK_828D30F58BAC62AF');
        $this->addSql('ALTER TABLE discovery_site_river DROP FOREIGN KEY FK_192F433F40B9017E');
        $this->addSql('ALTER TABLE discovery_site_city DROP FOREIGN KEY FK_828D30F540B9017E');
        $this->addSql('ALTER TABLE discovery_site_river DROP FOREIGN KEY FK_192F433F41E62266');
        $this->addSql('ALTER TABLE river DROP FOREIGN KEY FK_F5E3672B87DE2BB7');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE discovery_site');
        $this->addSql('DROP TABLE discovery_site_river');
        $this->addSql('DROP TABLE discovery_site_city');
        $this->addSql('DROP TABLE river');
        $this->addSql('ALTER TABLE carrier DROP carrier_history, DROP quadrat, DROP plast_level, DROP yarus_level, DROP depth');
    }
}
