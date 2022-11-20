<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221119142204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carrier_discovery_site (carrier_id INT NOT NULL, discovery_site_id INT NOT NULL, INDEX IDX_24643DA721DFC797 (carrier_id), INDEX IDX_24643DA740B9017E (discovery_site_id), PRIMARY KEY(carrier_id, discovery_site_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carrier_discovery_site ADD CONSTRAINT FK_24643DA721DFC797 FOREIGN KEY (carrier_id) REFERENCES carrier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrier_discovery_site ADD CONSTRAINT FK_24643DA740B9017E FOREIGN KEY (discovery_site_id) REFERENCES discovery_site (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE carrier_discovery_site');
    }
}
