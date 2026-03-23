<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Exception\IrreversibleMigration;

final class Version20260323120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Removes redundant ru localized_text rows for entities that already store base Russian content in main columns.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "DELETE FROM localized_text
             WHERE locale = 'ru'
               AND target_type IN ('inscription', 'carrier', 'zero_row', 'interpretation')"
        );
    }

    public function down(Schema $schema): void
    {
        throw new IrreversibleMigration(
            'This migration removes duplicated ru rows from localized_text and cannot be reverted automatically.'
        );
    }
}
