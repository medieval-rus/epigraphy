<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221119145845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carrier CHANGE plast_level plast_level LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE yarus_level yarus_level LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE city CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE country country VARCHAR(255) DEFAULT NULL, CHANGE region region VARCHAR(255) DEFAULT NULL, CHANGE name_aliases name_aliases LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE river CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE name_aliases name_aliases LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carrier CHANGE plast_level plast_level LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE yarus_level yarus_level LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE city CHANGE name_aliases name_aliases LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE type type VARCHAR(255) NOT NULL, CHANGE country country VARCHAR(255) NOT NULL, CHANGE region region VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE river CHANGE name_aliases name_aliases LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE type type VARCHAR(255) NOT NULL');
    }
}
