<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230123083951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C98E52EA5E237E06 ON discovery_site');
        $this->addSql('DROP INDEX UNIQ_606EC2F15E237E06 ON storage_site');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C98E52EA5E237E06 ON discovery_site (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_606EC2F15E237E06 ON storage_site (name)');
    }
}
