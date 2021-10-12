<?php

declare(strict_types=1);

/*
 * This file is part of «Epigraphy of Medieval Rus» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus» database is distributed
 * in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211011231743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added relation between photos and drawings on one side, and zero row and interpretation on the other.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE interpretation_photos (interpretation_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_7505E2E1F60A8F2C (interpretation_id), INDEX IDX_7505E2E193CB796C (file_id), PRIMARY KEY(interpretation_id, file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interpretation_drawings (interpretation_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_DC6873EBF60A8F2C (interpretation_id), INDEX IDX_DC6873EB93CB796C (file_id), PRIMARY KEY(interpretation_id, file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_photos (zero_row_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_6E0C0D3070A77CE4 (zero_row_id), INDEX IDX_6E0C0D3093CB796C (file_id), PRIMARY KEY(zero_row_id, file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_photos_references (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_2945CD570A77CE4 (zero_row_id), INDEX IDX_2945CD5F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_drawings (zero_row_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_74FE635570A77CE4 (zero_row_id), INDEX IDX_74FE635593CB796C (file_id), PRIMARY KEY(zero_row_id, file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zero_row_drawings_references (zero_row_id INT NOT NULL, interpretation_id INT NOT NULL, INDEX IDX_B942949370A77CE4 (zero_row_id), INDEX IDX_B9429493F60A8F2C (interpretation_id), PRIMARY KEY(zero_row_id, interpretation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interpretation_photos ADD CONSTRAINT FK_7505E2E1F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interpretation_photos ADD CONSTRAINT FK_7505E2E193CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interpretation_drawings ADD CONSTRAINT FK_DC6873EBF60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interpretation_drawings ADD CONSTRAINT FK_DC6873EB93CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_photos ADD CONSTRAINT FK_6E0C0D3070A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_photos ADD CONSTRAINT KF_6E0C0D3093CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_photos_references ADD CONSTRAINT FK_2945CD570A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_photos_references ADD CONSTRAINT FK_2945CD5F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_drawings ADD CONSTRAINT FK_74FE635570A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_drawings ADD CONSTRAINT KF_74FE635593CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_drawings_references ADD CONSTRAINT FK_B942949370A77CE4 FOREIGN KEY (zero_row_id) REFERENCES zero_row (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zero_row_drawings_references ADD CONSTRAINT FK_B9429493F60A8F2C FOREIGN KEY (interpretation_id) REFERENCES interpretation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE interpretation_photos');
        $this->addSql('DROP TABLE interpretation_drawings');
        $this->addSql('DROP TABLE zero_row_photos');
        $this->addSql('DROP TABLE zero_row_photos_references');
        $this->addSql('DROP TABLE zero_row_drawings');
        $this->addSql('DROP TABLE zero_row_drawings_references');
    }
}
