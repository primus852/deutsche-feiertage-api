<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Enum\HolidayRuleType;
use App\Util\MigrationHelper;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251008120000RegionalHolidays extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Erstelle regionale Feiertage (nicht bundesweit)';
    }

    public function up(Schema $schema): void
    {
        // Heilige Drei Könige (nur BW, BY, ST)
        $this->addSql("INSERT INTO holiday (id, name, description, created_at, updated_at) VALUES
            (gen_random_uuid(), 'Heilige Drei Könige', 'Epiphanias', NOW(), NOW())
        ");

        $fixed = HolidayRuleType::FIXED->value;
        $this->addSql("INSERT INTO holiday_rule (id, holiday_id, month, day, rule_type_id, offset_days, created_at, updated_at) VALUES
            (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Heilige Drei Könige'), 1, 6, (SELECT id FROM rule_type WHERE name = '$fixed'), NULL, NOW(), NOW())
        ");

        // Applicability nur für BW, BY, ST
        $applicableStates = ['BW', 'BY', 'ST'];
        foreach ($applicableStates as $symbol) {
            $this->addSql(
                "INSERT INTO holiday_applicability (id, holiday_id, state_id, created_at, updated_at) VALUES (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Heilige Drei Könige'), (SELECT id FROM state WHERE symbol = '" . $symbol . "'), NOW(), NOW())"
            );
        }

        // Internationaler Frauentag (nur BE, MV)
        $this->addSql("INSERT INTO holiday (id, name, description, created_at, updated_at) VALUES
            (gen_random_uuid(), 'Internationaler Frauentag', 'Internationaler Frauentag', NOW(), NOW())
        ");

        $this->addSql("INSERT INTO holiday_rule (id, holiday_id, month, day, rule_type_id, offset_days, created_at, updated_at) VALUES
            (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Internationaler Frauentag'), 3, 8, (SELECT id FROM rule_type WHERE name = '$fixed'), NULL, NOW(), NOW())
        ");

        // Applicability nur für BE, MV
        $applicableStates = ['BE', 'MV'];
        foreach ($applicableStates as $symbol) {
            $this->addSql(
                "INSERT INTO holiday_applicability (id, holiday_id, state_id, created_at, updated_at) VALUES (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Internationaler Frauentag'), (SELECT id FROM state WHERE symbol = '" . $symbol . "'), NOW(), NOW())"
            );
        }

        // Weltkindertag (nur TH)
        $this->addSql("INSERT INTO holiday (id, name, description, created_at, updated_at) VALUES
            (gen_random_uuid(), 'Weltkindertag', 'Weltkindertag', NOW(), NOW())
        ");

        $this->addSql("INSERT INTO holiday_rule (id, holiday_id, month, day, rule_type_id, offset_days, created_at, updated_at) VALUES
            (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Weltkindertag'), 9, 20, (SELECT id FROM rule_type WHERE name = '$fixed'), NULL, NOW(), NOW())
        ");

        // Applicability nur für TH
        $this->addSql(
            "INSERT INTO holiday_applicability (id, holiday_id, state_id, created_at, updated_at) VALUES (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Weltkindertag'), (SELECT id FROM state WHERE symbol = 'TH'), NOW(), NOW())"
        );

        // Reformationstag (BB, MV, SN, ST, TH, HB, HH, NI, SH)
        $this->addSql("INSERT INTO holiday (id, name, description, created_at, updated_at) VALUES
            (gen_random_uuid(), 'Reformationstag', 'Reformationstag', NOW(), NOW())
        ");

        $this->addSql("INSERT INTO holiday_rule (id, holiday_id, month, day, rule_type_id, offset_days, created_at, updated_at) VALUES
            (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Reformationstag'), 10, 31, (SELECT id FROM rule_type WHERE name = '$fixed'), NULL, NOW(), NOW())
        ");

        // Applicability für BB, MV, SN, ST, TH, HB, HH, NI, SH
        $applicableStates = ['BB', 'MV', 'SN', 'ST', 'TH', 'HB', 'HH', 'NI', 'SH'];
        foreach ($applicableStates as $symbol) {
            $this->addSql(
                "INSERT INTO holiday_applicability (id, holiday_id, state_id, created_at, updated_at) VALUES (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Reformationstag'), (SELECT id FROM state WHERE symbol = '" . $symbol . "'), NOW(), NOW())"
            );
        }

        // Allerheiligen (BW, BY, NW, RP, SL)
        $this->addSql("INSERT INTO holiday (id, name, description, created_at, updated_at) VALUES
            (gen_random_uuid(), 'Allerheiligen', 'Allerheiligen', NOW(), NOW())
        ");

        $this->addSql("INSERT INTO holiday_rule (id, holiday_id, month, day, rule_type_id, offset_days, created_at, updated_at) VALUES
            (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Allerheiligen'), 11, 1, (SELECT id FROM rule_type WHERE name = '$fixed'), NULL, NOW(), NOW())
        ");

        // Applicability für BW, BY, NW, RP, SL
        $applicableStates = ['BW', 'BY', 'NW', 'RP', 'SL'];
        foreach ($applicableStates as $symbol) {
            $this->addSql(
                "INSERT INTO holiday_applicability (id, holiday_id, state_id, created_at, updated_at) VALUES (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Allerheiligen'), (SELECT id FROM state WHERE symbol = '" . $symbol . "'), NOW(), NOW())"
            );
        }

        // Buß- und Bettag (nur SN)
        $this->addSql("INSERT INTO holiday (id, name, description, created_at, updated_at) VALUES
            (gen_random_uuid(), 'Buß- und Bettag', 'Buß- und Bettag', NOW(), NOW())
        ");

        // Der Buß- und Bettag ist immer am Mittwoch vor dem letzten Sonntag des Kirchenjahres
        // Einfacher: Wir erstellen für jedes Jahr eine ONE_OFF Regel
        $oneOff = HolidayRuleType::ONE_OFF->value;

        // Buß- und Bettag Termine für 2024-2030
        $bussUndBettag = [
            2024 => ['month' => 11, 'day' => 20],
            2025 => ['month' => 11, 'day' => 19],
            2026 => ['month' => 11, 'day' => 18],
            2027 => ['month' => 11, 'day' => 17],
            2028 => ['month' => 11, 'day' => 22],
            2029 => ['month' => 11, 'day' => 21],
            2030 => ['month' => 11, 'day' => 20],
        ];

        foreach ($bussUndBettag as $year => $date) {
            $this->addSql("INSERT INTO holiday_rule (id, holiday_id, month, day, year, rule_type_id, offset_days, created_at, updated_at) VALUES
                (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Buß- und Bettag'), {$date['month']}, {$date['day']}, {$year}, (SELECT id FROM rule_type WHERE name = '$oneOff'), NULL, NOW(), NOW())
            ");
        }

        // Applicability nur für SN
        $this->addSql(
            "INSERT INTO holiday_applicability (id, holiday_id, state_id, created_at, updated_at) VALUES (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Buß- und Bettag'), (SELECT id FROM state WHERE symbol = 'SN'), NOW(), NOW())"
        );

        // Mariä Himmelfahrt (BY, SL - in BY nur in überwiegend katholischen Gemeinden, vereinfacht hier für ganz BY)
        $this->addSql("INSERT INTO holiday (id, name, description, created_at, updated_at) VALUES
            (gen_random_uuid(), 'Mariä Himmelfahrt', 'Mariä Himmelfahrt', NOW(), NOW())
        ");

        $this->addSql("INSERT INTO holiday_rule (id, holiday_id, month, day, rule_type_id, offset_days, created_at, updated_at) VALUES
            (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Mariä Himmelfahrt'), 8, 15, (SELECT id FROM rule_type WHERE name = '$fixed'), NULL, NOW(), NOW())
        ");

        // Applicability für BY, SL
        $applicableStates = ['BY', 'SL'];
        foreach ($applicableStates as $symbol) {
            $this->addSql(
                "INSERT INTO holiday_applicability (id, holiday_id, state_id, created_at, updated_at) VALUES (gen_random_uuid(), (SELECT id FROM holiday WHERE name = 'Mariä Himmelfahrt'), (SELECT id FROM state WHERE symbol = '" . $symbol . "'), NOW(), NOW())"
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM holiday WHERE name IN (
            'Heilige Drei Könige',
            'Internationaler Frauentag',
            'Weltkindertag',
            'Reformationstag',
            'Allerheiligen',
            'Buß- und Bettag',
            'Mariä Himmelfahrt'
        )");
    }
}

