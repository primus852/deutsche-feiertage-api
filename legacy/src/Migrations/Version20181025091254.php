<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181025091254 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, holiday_id INT NOT NULL, comment LONGTEXT NOT NULL, INDEX IDX_9474526C830A3EC0 (holiday_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE holiday (id INT AUTO_INCREMENT NOT NULL, holiday_day INT NOT NULL, holiday_month INT NOT NULL, holiday_year INT DEFAULT NULL, bundesweit TINYINT(1) NOT NULL, bw TINYINT(1) NOT NULL, bay TINYINT(1) NOT NULL, be TINYINT(1) NOT NULL, bb TINYINT(1) NOT NULL, hb TINYINT(1) NOT NULL, hh TINYINT(1) NOT NULL, he TINYINT(1) NOT NULL, mv TINYINT(1) NOT NULL, ni TINYINT(1) NOT NULL, nw TINYINT(1) NOT NULL, rp TINYINT(1) NOT NULL, sl TINYINT(1) NOT NULL, sn TINYINT(1) NOT NULL, st TINYINT(1) NOT NULL, sh TINYINT(1) NOT NULL, th TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C830A3EC0 FOREIGN KEY (holiday_id) REFERENCES holiday (id)');
        $this->addSql('DROP TABLE holidays');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C830A3EC0');
        $this->addSql('CREATE TABLE holidays (id INT AUTO_INCREMENT NOT NULL, holiday_day INT NOT NULL, holiday_month INT NOT NULL, holiday_year INT DEFAULT NULL, holiday_name TEXT DEFAULT NULL COLLATE latin1_swedish_ci, bw TINYINT(1) NOT NULL, bw_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, bay TINYINT(1) NOT NULL, bay_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, be TINYINT(1) NOT NULL, be_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, bb TINYINT(1) NOT NULL, bb_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, hb TINYINT(1) NOT NULL, hb_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, hh TINYINT(1) NOT NULL, hh_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, he TINYINT(1) NOT NULL, he_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, mv TINYINT(1) NOT NULL, mv_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, ni TINYINT(1) NOT NULL, ni_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, nw TINYINT(1) NOT NULL, nw_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, rp TINYINT(1) NOT NULL, rp_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, sl TINYINT(1) NOT NULL, sl_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, sn TINYINT(1) NOT NULL, sn_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, st TINYINT(1) NOT NULL, st_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, sh TINYINT(1) NOT NULL, sh_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, th TINYINT(1) NOT NULL, th_comment TEXT DEFAULT NULL COLLATE latin1_swedish_ci, bundesweit TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE holiday');
    }
}
