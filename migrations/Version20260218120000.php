<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260218120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds localized text table for non-categorical translated fields.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE localized_text (
                id INT AUTO_INCREMENT NOT NULL,
                target_type VARCHAR(32) NOT NULL,
                target_id INT NOT NULL,
                field VARCHAR(64) NOT NULL,
                locale VARCHAR(5) NOT NULL,
                value LONGTEXT NOT NULL,
                INDEX localized_text_target_idx (target_type, target_id),
                INDEX localized_text_locale_idx (locale),
                INDEX localized_text_field_idx (field),
                UNIQUE INDEX localized_text_unique_target_field_locale (target_type, target_id, field, locale),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

        // inscription
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'inscription', id, 'dateExplanation', 'ru', date_explanation
            FROM inscription WHERE date_explanation IS NOT NULL AND date_explanation <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'inscription', id, 'comment', 'ru', comment
            FROM inscription WHERE comment IS NOT NULL AND comment <> ''");

        // carrier
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'carrier', id, 'origin1', 'ru', origin1
            FROM carrier WHERE origin1 IS NOT NULL AND origin1 <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'carrier', id, 'origin2', 'ru', origin2
            FROM carrier WHERE origin2 IS NOT NULL AND origin2 <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'carrier', id, 'findCircumstances', 'ru', find_circumstances
            FROM carrier WHERE find_circumstances IS NOT NULL AND find_circumstances <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'carrier', id, 'carrierHistory', 'ru', carrier_history
            FROM carrier WHERE carrier_history IS NOT NULL AND carrier_history <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'carrier', id, 'archaeology', 'ru', archaeology
            FROM carrier WHERE archaeology IS NOT NULL AND archaeology <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'carrier', id, 'characteristics', 'ru', characteristics
            FROM carrier WHERE characteristics IS NOT NULL AND characteristics <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'carrier', id, 'individualName', 'ru', individual_name
            FROM carrier WHERE individual_name IS NOT NULL AND individual_name <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'carrier', id, 'storagePlace', 'ru', storage_place
            FROM carrier WHERE storage_place IS NOT NULL AND storage_place <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'carrier', id, 'inventoryNumber', 'ru', inventory_number
            FROM carrier WHERE inventory_number IS NOT NULL AND inventory_number <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'carrier', id, 'stratigraphicalDate', 'ru', stratigraphical_date
            FROM carrier WHERE stratigraphical_date IS NOT NULL AND stratigraphical_date <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'carrier', id, 'previousStorage', 'ru', previous_storage
            FROM carrier WHERE previous_storage IS NOT NULL AND previous_storage <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'carrier', id, 'storageLocalization', 'ru', storage_localization
            FROM carrier WHERE storage_localization IS NOT NULL AND storage_localization <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'carrier', id, 'materialDescription', 'ru', material_description
            FROM carrier WHERE material_description IS NOT NULL AND material_description <> ''");

        // zero_row
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'zero_row', id, 'origin', 'ru', origin
            FROM zero_row WHERE origin IS NOT NULL AND origin <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'zero_row', id, 'placeOnCarrier', 'ru', place_on_carrier
            FROM zero_row WHERE place_on_carrier IS NOT NULL AND place_on_carrier <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'zero_row', id, 'interpretationComment', 'ru', interpretation_comment
            FROM zero_row WHERE interpretation_comment IS NOT NULL AND interpretation_comment <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'zero_row', id, 'text', 'ru', text
            FROM zero_row WHERE text IS NOT NULL AND text <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'zero_row', id, 'transliteration', 'ru', transliteration
            FROM zero_row WHERE transliteration IS NOT NULL AND transliteration <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'zero_row', id, 'reconstruction', 'ru', reconstruction
            FROM zero_row WHERE reconstruction IS NOT NULL AND reconstruction <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'zero_row', id, 'normalization', 'ru', normalization
            FROM zero_row WHERE normalization IS NOT NULL AND normalization <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'zero_row', id, 'translation', 'ru', translation
            FROM zero_row WHERE translation IS NOT NULL AND translation <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'zero_row', id, 'description', 'ru', description
            FROM zero_row WHERE description IS NOT NULL AND description <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'zero_row', id, 'dateInText', 'ru', date_in_text
            FROM zero_row WHERE date_in_text IS NOT NULL AND date_in_text <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'zero_row', id, 'nonStratigraphicalDate', 'ru', non_stratigraphical_date
            FROM zero_row WHERE non_stratigraphical_date IS NOT NULL AND non_stratigraphical_date <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'zero_row', id, 'historicalDate', 'ru', historical_date
            FROM zero_row WHERE historical_date IS NOT NULL AND historical_date <> ''");

        // interpretation
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'interpretation', id, 'comment', 'ru', comment
            FROM interpretation WHERE comment IS NOT NULL AND comment <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'interpretation', id, 'origin', 'ru', origin
            FROM interpretation WHERE origin IS NOT NULL AND origin <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'interpretation', id, 'placeOnCarrier', 'ru', place_on_carrier
            FROM interpretation WHERE place_on_carrier IS NOT NULL AND place_on_carrier <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'interpretation', id, 'interpretationComment', 'ru', interpretation_comment
            FROM interpretation WHERE interpretation_comment IS NOT NULL AND interpretation_comment <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'interpretation', id, 'text', 'ru', text
            FROM interpretation WHERE text IS NOT NULL AND text <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'interpretation', id, 'transliteration', 'ru', transliteration
            FROM interpretation WHERE transliteration IS NOT NULL AND transliteration <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'interpretation', id, 'reconstruction', 'ru', reconstruction
            FROM interpretation WHERE reconstruction IS NOT NULL AND reconstruction <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'interpretation', id, 'normalization', 'ru', normalization
            FROM interpretation WHERE normalization IS NOT NULL AND normalization <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'interpretation', id, 'translation', 'ru', translation
            FROM interpretation WHERE translation IS NOT NULL AND translation <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'interpretation', id, 'description', 'ru', description
            FROM interpretation WHERE description IS NOT NULL AND description <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'interpretation', id, 'dateInText', 'ru', date_in_text
            FROM interpretation WHERE date_in_text IS NOT NULL AND date_in_text <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'interpretation', id, 'nonStratigraphicalDate', 'ru', non_stratigraphical_date
            FROM interpretation WHERE non_stratigraphical_date IS NOT NULL AND non_stratigraphical_date <> ''");
        $this->addSql("INSERT INTO localized_text (target_type, target_id, field, locale, value)
            SELECT 'interpretation', id, 'historicalDate', 'ru', historical_date
            FROM interpretation WHERE historical_date IS NOT NULL AND historical_date <> ''");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE localized_text');
    }
}
