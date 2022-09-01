<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220817223959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carrier_category ADD is_super_category TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE content_category ADD is_super_category TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE material ADD is_super_material TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE writing_method ADD is_super_method TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carrier_category DROP is_super_category');
        $this->addSql('ALTER TABLE content_category DROP is_super_category');
        $this->addSql('ALTER TABLE material DROP is_super_material');
        $this->addSql('ALTER TABLE writing_method DROP is_super_method');
    }
}
