<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Enum\HolidayRuleType;
use App\Util\MigrationHelper;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251002194226FixedDates extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Erstelle die bundesweiten Feiertage';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO holiday (id, name, description, created_at, updated_at) VALUES
            (gen_random_uuid(), 'Neujahr', 'Neujahrstag', NOW(), NOW()),
            (gen_random_uuid(), 'Tag der Arbeit', 'Tag der Arbeitstag', NOW(), NOW()),
            (gen_random_uuid(), 'Tag der Deutschen Einheit', 'Tag der Deutschen Einheit', NOW(), NOW()),
            (gen_random_uuid(), 'Erster Weihnachtstag', 'Erster Weihnachtstag', NOW(), NOW()),
            (gen_random_uuid(), 'Zweiter Weihnachtstag', 'Zweiter Weihnachtstag', NOW(), NOW())
        ");

        // Regeln für die Feiertage einfügen
        $fixed = HolidayRuleType::FIXED->value;
        $this->addSql("INSERT INTO holiday_rule (id, holiday_id, month, day, rule_type_id, offset_days, created_at, updated_at) VALUES
            (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Neujahr'), 1, 1, (SELECT id FROM rule_type WHERE name = '$fixed'), NULL, NOW(), NOW()),
            (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Tag der Arbeit'), 5, 1, (SELECT id FROM rule_type WHERE name = '$fixed'), NULL, NOW(), NOW()),
            (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Tag der Deutschen Einheit'), 10, 3, (SELECT id FROM rule_type WHERE name = '$fixed'), NULL, NOW(), NOW()),
            (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Erster Weihnachtstag'), 12, 25, (SELECT id FROM rule_type WHERE name = '$fixed'), NULL, NOW(), NOW()),
            (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Zweiter Weihnachtstag'), 12, 26, (SELECT id FROM rule_type WHERE name = '$fixed'), NULL, NOW(), NOW())
        ");

        // Applicability für alle Feiertage und alle Bundesländer
        $holidayNames = [
            'Neujahr',
            'Tag der Arbeit',
            'Tag der Deutschen Einheit',
            'Erster Weihnachtstag',
            'Zweiter Weihnachtstag'
        ];

        foreach ($holidayNames as $holidayName) {
            foreach (MigrationHelper::getStates() as $symbol => $state) {
                $this->addSql(
                    "INSERT INTO holiday_applicability (id, holiday_id, state_id, created_at, updated_at) VALUES (gen_random_uuid(), (SELECT id FROM holiday WHERE name = '" . $holidayName . "'), (SELECT id FROM state WHERE symbol = '" . $symbol . "'), NOW(), NOW())"
                );
            }
        }

    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM holiday WHERE name IN ('Neujahr', 'Tag der Arbeit', 'Tag der Deutschen Einheit', 'Erster Weihnachtstag', 'Zweiter Weihnachtstag')");
    }
}
