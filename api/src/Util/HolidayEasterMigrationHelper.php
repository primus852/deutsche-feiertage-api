<?php

namespace App\Util;

use App\Enum\HolidayRuleType;
use Doctrine\Migrations\AbstractMigration;

class HolidayEasterMigrationHelper
{
    /**
     * Creates SQL for one-time relative Easter holidays and year-specific Easter Sunday holidays
     */
    public static function getEasterRelatedHolidaySql(array $easterDates): array
    {
        $sql = [];

        // Create the relative Easter holidays (only once, not per year)
        $sql = array_merge($sql, self::createRelativeEasterHolidays());

        // Create year-specific Easter Sunday holidays
        return array_merge($sql, self::createYearSpecificEasterSundays($easterDates));
    }

    /**
     * Creates relative Easter holidays that don't need to be created per year
     * These are calculated dynamically based on the Easter date of any given year
     */
    private static function createRelativeEasterHolidays(): array
    {
        $sql = [];
        $relativeToEaster = HolidayRuleType::RELATIVE_TO_EASTER->value;

        $relativeHolidays = [
            [
                'name' => 'Karfreitag',
                'description' => 'Karfreitag (Ostersonntag - 2 Tage)',
                'offset' => -2,
                'states' => array_keys(MigrationHelper::getStates()) // All states
            ],
            [
                'name' => 'Ostermontag',
                'description' => 'Ostermontag (Ostersonntag + 1 Tag)',
                'offset' => 1,
                'states' => array_keys(MigrationHelper::getStates()) // All states
            ],
            [
                'name' => 'Christi Himmelfahrt',
                'description' => 'Christi Himmelfahrt (Ostersonntag + 39 Tage)',
                'offset' => 39,
                'states' => array_keys(MigrationHelper::getStates()) // All states
            ],
            [
                'name' => 'Pfingstsonntag',
                'description' => 'Pfingstsonntag (Ostersonntag + 49 Tage)',
                'offset' => 49,
                'states' => array_keys(MigrationHelper::getStates()) // All states
            ],
            [
                'name' => 'Pfingstmontag',
                'description' => 'Pfingstmontag (Ostersonntag + 50 Tage)',
                'offset' => 50,
                'states' => array_keys(MigrationHelper::getStates()) // All states
            ],
            [
                'name' => 'Fronleichnam',
                'description' => 'Fronleichnam (Ostersonntag + 60 Tage)',
                'offset' => 60,
                'states' => ['BW', 'BY', 'HE', 'NW', 'RP', 'SL'] // Only specific states
            ]
        ];

        foreach ($relativeHolidays as $holiday) {
            // Create holiday entry (without year in description since it's calculated dynamically)
            $sql[] = "INSERT INTO holiday (id, name, description, created_at, updated_at) VALUES (gen_random_uuid(), '{$holiday['name']}', '{$holiday['description']}', NOW(), NOW())";

            // Create holiday rule entry (no specific year, month, or day - only offset)
            $sql[] = "INSERT INTO holiday_rule (id, holiday_id, month, day, year, rule_type_id, offset_days, created_at, updated_at) VALUES (
                gen_random_uuid(),
                (SELECT id FROM holiday WHERE name = '{$holiday['name']}' AND description = '{$holiday['description']}'),
                NULL, NULL, NULL,
                (SELECT id FROM rule_type WHERE name = '$relativeToEaster'),
                {$holiday['offset']}, NOW(), NOW()
            )";

            // Create holiday applicability entries for relevant states
            foreach ($holiday['states'] as $symbol) {
                $sql[] = "INSERT INTO holiday_applicability (id, holiday_id, state_id, created_at, updated_at) VALUES (
                    gen_random_uuid(),
                    (SELECT id FROM holiday WHERE name = '{$holiday['name']}' AND description = '{$holiday['description']}'),
                    (SELECT id FROM state WHERE symbol = '$symbol'),
                    NOW(), NOW()
                )";
            }
        }

        return $sql;
    }

    /**
     * Creates year-specific Easter Sunday holidays
     * These need specific year entries because Easter has different dates each year
     */
    private static function createYearSpecificEasterSundays(array $easterDates): array
    {
        $sql = [];
        $easter = HolidayRuleType::EASTER->value;

        foreach ($easterDates as $year => $date) {
            // Ostersonntag (year-specific because it has a fixed date for each year)
            $holidayName = 'Ostersonntag';
            $description = "Ostersonntag $year";

            $sql[] = "INSERT INTO holiday (id, name, description, created_at, updated_at) VALUES (gen_random_uuid(), '$holidayName', '$description', NOW(), NOW())";

            $sql[] = "INSERT INTO holiday_rule (id, holiday_id, month, day, year, rule_type_id, offset_days, created_at, updated_at) VALUES (
                gen_random_uuid(),
                (SELECT id FROM holiday WHERE name = '$holidayName' AND description = '$description'),
                {$date['month']}, {$date['day']}, $year,
                (SELECT id FROM rule_type WHERE name = '$easter'),
                NULL, NOW(), NOW()
            )";

            foreach (MigrationHelper::getStates() as $symbol => $state) {
                $sql[] = "INSERT INTO holiday_applicability (id, holiday_id, state_id, created_at, updated_at) VALUES (
                    gen_random_uuid(),
                    (SELECT id FROM holiday WHERE name = '$holidayName' AND description = '$description'),
                    (SELECT id FROM state WHERE symbol = '$symbol'),
                    NOW(), NOW()
                )";
            }
        }

        return $sql;
    }
}
