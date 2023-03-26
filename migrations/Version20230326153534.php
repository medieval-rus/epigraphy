<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230326153534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE zero_row_interpretation_comment_references (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_617237DF70A77CE4 (zero_row_id), INDEX IDX_617237DFF60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE zero_row_interpretation_comment_references ADD CONSTRAINT FK_617237DF70A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_interpretation_comment_references ADD CONSTRAINT FK_617237DFF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE zero_row_interpretation_comment_references');
    }
}
