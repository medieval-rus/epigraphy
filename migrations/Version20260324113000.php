<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260324113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds epidoc_document table and one-to-one relation from inscription.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE epidoc_document (
                id INT AUTO_INCREMENT NOT NULL,
                xml LONGTEXT NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

        $this->addSql('ALTER TABLE inscription ADD epidoc_document_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_INSCRIPTION_EPIDOC_DOCUMENT ON inscription (epidoc_document_id)');
        $this->addSql(
            'ALTER TABLE inscription
             ADD CONSTRAINT FK_INSCRIPTION_EPIDOC_DOCUMENT
             FOREIGN KEY (epidoc_document_id) REFERENCES epidoc_document (id) ON DELETE SET NULL'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_INSCRIPTION_EPIDOC_DOCUMENT');
        $this->addSql('DROP INDEX UNIQ_INSCRIPTION_EPIDOC_DOCUMENT ON inscription');
        $this->addSql('ALTER TABLE inscription DROP epidoc_document_id');
        $this->addSql('DROP TABLE epidoc_document');
    }
}
