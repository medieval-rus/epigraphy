<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221119144648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discovery_site CHANGE name_aliases name_aliases LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE comments comments VARCHAR(255) DEFAULT NULL, CHANGE latitude latitude INT DEFAULT NULL, CHANGE longitude longitude INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discovery_site CHANGE name_aliases name_aliases LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE comments comments VARCHAR(255) NOT NULL, CHANGE latitude latitude INT NOT NULL, CHANGE longitude longitude INT NOT NULL');
    }
}
