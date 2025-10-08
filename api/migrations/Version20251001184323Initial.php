<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251001184323Initial extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE holiday (id UUID NOT NULL, name VARCHAR(100) NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN holiday.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holiday.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN holiday.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE holiday_applicability (id UUID NOT NULL, holiday_id UUID NOT NULL, state_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_926D35B6830A3EC0 ON holiday_applicability (holiday_id)');
        $this->addSql('CREATE INDEX IDX_926D35B65D83CC1 ON holiday_applicability (state_id)');
        $this->addSql('COMMENT ON COLUMN holiday_applicability.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holiday_applicability.holiday_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holiday_applicability.state_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holiday_applicability.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN holiday_applicability.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE holiday_rule (id UUID NOT NULL, rule_type_id UUID DEFAULT NULL, holiday_id UUID NOT NULL, month INT DEFAULT NULL, day INT DEFAULT NULL, year INT DEFAULT NULL, offset_days INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_252EF3B535D8B527 ON holiday_rule (rule_type_id)');
        $this->addSql('CREATE INDEX IDX_252EF3B5830A3EC0 ON holiday_rule (holiday_id)');
        $this->addSql('COMMENT ON COLUMN holiday_rule.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holiday_rule.rule_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holiday_rule.holiday_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holiday_rule.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN holiday_rule.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE refresh_tokens (id SERIAL NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token)');
        $this->addSql('CREATE TABLE rule_type (id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DF1A085E77153098 ON rule_type (name)');
        $this->addSql('COMMENT ON COLUMN rule_type.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN rule_type.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN rule_type.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE state (id UUID NOT NULL, symbol VARCHAR(3) NOT NULL, name VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN state.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN state.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN state.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE holiday_applicability ADD CONSTRAINT FK_926D35B6830A3EC0 FOREIGN KEY (holiday_id) REFERENCES holiday (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holiday_applicability ADD CONSTRAINT FK_926D35B65D83CC1 FOREIGN KEY (state_id) REFERENCES state (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holiday_rule ADD CONSTRAINT FK_252EF3B535D8B527 FOREIGN KEY (rule_type_id) REFERENCES rule_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holiday_rule ADD CONSTRAINT FK_252EF3B5830A3EC0 FOREIGN KEY (holiday_id) REFERENCES holiday (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE holiday_applicability DROP CONSTRAINT FK_926D35B6830A3EC0');
        $this->addSql('ALTER TABLE holiday_applicability DROP CONSTRAINT FK_926D35B65D83CC1');
        $this->addSql('ALTER TABLE holiday_rule DROP CONSTRAINT FK_252EF3B535D8B527');
        $this->addSql('ALTER TABLE holiday_rule DROP CONSTRAINT FK_252EF3B5830A3EC0');
        $this->addSql('DROP TABLE holiday');
        $this->addSql('DROP TABLE holiday_applicability');
        $this->addSql('DROP TABLE holiday_rule');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE rule_type');
        $this->addSql('DROP TABLE state');
        $this->addSql('DROP TABLE "user"');
    }
}
