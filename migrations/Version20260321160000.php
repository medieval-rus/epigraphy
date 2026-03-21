<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260321160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds AI translation marker to localized text records.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE localized_text ADD is_ai_generated TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE localized_text DROP is_ai_generated');
    }
}
