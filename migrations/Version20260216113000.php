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

final class Version20260216113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds carrier category translations.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE carrier_category_translation (
                id INT AUTO_INCREMENT NOT NULL,
                carrier_category_id INT NOT NULL,
                locale VARCHAR(5) NOT NULL,
                name VARCHAR(255) NOT NULL,
                INDEX IDX_CARRIER_CATEGORY_TRANSLATION_CATEGORY (carrier_category_id),
                INDEX carrier_category_translation_locale_idx (locale),
                INDEX carrier_category_translation_name_idx (name),
                UNIQUE INDEX carrier_category_locale_unique (carrier_category_id, locale),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE carrier_category_translation
             ADD CONSTRAINT FK_CARRIER_CATEGORY_TRANSLATION_CATEGORY
             FOREIGN KEY (carrier_category_id) REFERENCES carrier_category (id) ON DELETE CASCADE'
        );
        $this->addSql(
            "INSERT INTO carrier_category_translation (carrier_category_id, locale, name)
             SELECT id, 'ru', name FROM carrier_category"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE carrier_category_translation');
    }
}
