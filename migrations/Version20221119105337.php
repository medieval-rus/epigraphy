<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221119105337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE city ADD name_aliases LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE river ADD name_aliases LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE name name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE city DROP name_aliases, CHANGE name name LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE river DROP name_aliases, CHANGE name name LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\'');
    }
}
