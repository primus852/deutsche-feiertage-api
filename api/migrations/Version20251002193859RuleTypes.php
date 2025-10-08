<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Enum\HolidayRuleType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251002193859RuleTypes extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Erstelle die RuleTypes für die Feiertagsberechnung';
    }

    public function up(Schema $schema): void
    {
        $fixed = HolidayRuleType::FIXED->value;
        $easter = HolidayRuleType::EASTER->value;
        $relativeToEaster = HolidayRuleType::RELATIVE_TO_EASTER->value;
        $oneOff = HolidayRuleType::ONE_OFF->value;

        // Insert RuleTypes
        $this->addSql("INSERT INTO rule_type (id, name, description, created_at, updated_at) VALUES
            (gen_random_uuid(), '$fixed', 'Feiertage jedes Jahr', NOW(), NOW()),
            (gen_random_uuid(), '$easter', 'Ostersonntag', NOW(), NOW()),
            (gen_random_uuid(), '$relativeToEaster', 'Relativ zu Ostersonntag', NOW(), NOW()),
            (gen_random_uuid(), '$oneOff', 'Einmalige Feiertage', NOW(), NOW())
        ");
    }

    public function down(Schema $schema): void
    {
        $fixed = HolidayRuleType::FIXED->value;
        $easter = HolidayRuleType::EASTER->value;
        $relativeToEaster = HolidayRuleType::RELATIVE_TO_EASTER->value;
        $oneOff = HolidayRuleType::ONE_OFF->value;
        // Delete RuleTypes
        $this->addSql("DELETE FROM rule_type WHERE name IN ('$fixed', '$easter', '$relativeToEaster', '$oneOff')");
    }
}
