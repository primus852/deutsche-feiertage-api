<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Util\HolidayEasterMigrationHelper;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251002195756Easter24To30 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ostern für 2024 bis 2030 einfügen';
    }

    public function up(Schema $schema): void
    {
        $easterDates = [
            2024 => ['month' => 3, 'day' => 31],
            2025 => ['month' => 4, 'day' => 20],
            2026 => ['month' => 4, 'day' => 5],
            2027 => ['month' => 3, 'day' => 28],
            2028 => ['month' => 4, 'day' => 16],
            2029 => ['month' => 4, 'day' => 1],
            2030 => ['month' => 4, 'day' => 21],
        ];
        foreach (HolidayEasterMigrationHelper::getEasterRelatedHolidaySql($easterDates) as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM holiday WHERE name = 'Ostersonntag'");
        $this->addSql("DELETE FROM holiday WHERE name = 'Karfreitag'");
        $this->addSql("DELETE FROM holiday WHERE name = 'Ostermontag'");
        $this->addSql("DELETE FROM holiday WHERE name = 'Christi Himmelfahrt'");
        $this->addSql("DELETE FROM holiday WHERE name = 'Pfingstsonntag'");
        $this->addSql("DELETE FROM holiday WHERE name = 'Pfingstmontag'");
        $this->addSql("DELETE FROM holiday WHERE name = 'Fronleichnam'");
    }
}
