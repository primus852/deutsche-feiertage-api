<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Util\MigrationHelper;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251002183356States extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fügt alle deutschen Bundesländer zur Tabelle state hinzu';
    }

    public function up(Schema $schema): void
    {

        foreach (MigrationHelper::getStates() as $symbol => $state) {
            $this->addSql(sprintf(
                "INSERT INTO state (id, symbol, name, created_at, updated_at) VALUES (gen_random_uuid(), '%s', '%s', NOW(), NOW())",
                $symbol,
                $state
            ));
        }
    }

    public function down(Schema $schema): void
    {
        foreach (MigrationHelper::getStates() as $symbol => $state) {
            $this->addSql(sprintf(
                "DELETE FROM state WHERE symbol = '%s'",
                $symbol
            ));
        }
    }
}
