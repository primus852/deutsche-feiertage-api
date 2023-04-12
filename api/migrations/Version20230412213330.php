<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230412213330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE holiday (id UUID NOT NULL, holiday_day INT NOT NULL, holiday_month INT NOT NULL, holiday_year INT DEFAULT NULL, is_general BOOLEAN NOT NULL, bw BOOLEAN NOT NULL, bay BOOLEAN NOT NULL, be BOOLEAN NOT NULL, bb BOOLEAN NOT NULL, hb BOOLEAN NOT NULL, hh BOOLEAN NOT NULL, he BOOLEAN NOT NULL, mv BOOLEAN NOT NULL, ni BOOLEAN NOT NULL, nw BOOLEAN NOT NULL, rp BOOLEAN NOT NULL, sl BOOLEAN NOT NULL, sn BOOLEAN NOT NULL, st BOOLEAN NOT NULL, sh BOOLEAN NOT NULL, th BOOLEAN NOT NULL, holiday_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN holiday.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holiday.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN holiday.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE holiday');
    }
}
