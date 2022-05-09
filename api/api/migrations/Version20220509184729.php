<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220509184729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE holiday (id UUID NOT NULL, holiday_day INT NOT NULL, holiday_month INT NOT NULL, holiday_year INT DEFAULT NULL, holiday_name VARCHAR(255) NOT NULL, is_bundesweit BOOLEAN NOT NULL, is_bw BOOLEAN NOT NULL, is_bay BOOLEAN NOT NULL, is_be BOOLEAN NOT NULL, is_bb BOOLEAN NOT NULL, is_hb BOOLEAN NOT NULL, is_hh BOOLEAN NOT NULL, is_he BOOLEAN NOT NULL, is_mv BOOLEAN NOT NULL, is_ni BOOLEAN NOT NULL, is_nw BOOLEAN NOT NULL, is_rp BOOLEAN NOT NULL, is_sl BOOLEAN NOT NULL, is_sn BOOLEAN NOT NULL, is_st BOOLEAN NOT NULL, is_sh BOOLEAN NOT NULL, is_th BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN holiday.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE holiday_comment (id UUID NOT NULL, holiday_id UUID NOT NULL, comment TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A6A91762830A3EC0 ON holiday_comment (holiday_id)');
        $this->addSql('COMMENT ON COLUMN holiday_comment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holiday_comment.holiday_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE holiday_comment ADD CONSTRAINT FK_A6A91762830A3EC0 FOREIGN KEY (holiday_id) REFERENCES holiday (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE holiday_comment DROP CONSTRAINT FK_A6A91762830A3EC0');
        $this->addSql('DROP TABLE holiday');
        $this->addSql('DROP TABLE holiday_comment');
    }
}
