<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190317152952 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE schedule_log DROP FOREIGN KEY FK_12F62DCFA35860F2');
        $this->addSql('DROP TABLE channel');
        $this->addSql('DROP TABLE schedule_log');
        $this->addSql('DROP TABLE stream_schedule');
        $this->addSql('DROP INDEX UNIQ_C250282492FC23A8 ON app_users');
        $this->addSql('DROP INDEX UNIQ_C2502824C05FB297 ON app_users');
        $this->addSql('DROP INDEX UNIQ_C2502824A0D96FBF ON app_users');
        $this->addSql('ALTER TABLE app_users DROP username_canonical, DROP email_canonical, DROP salt, DROP last_login, DROP confirmation_token, DROP password_requested_at, DROP locale, DROP channel, CHANGE username username VARCHAR(25) NOT NULL, CHANGE email email VARCHAR(254) NOT NULL, CHANGE password password VARCHAR(64) NOT NULL, CHANGE enabled is_active TINYINT(1) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2502824F85E0677 ON app_users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2502824E7927C74 ON app_users (email)');
        $this->addSql('CREATE TABLE channel (name VARCHAR(100) NOT NULL, UNIQUE INDEX name (name), PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('INSERT INTO `channel` (`name`) VALUES (\'temporary-channel-name\');');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE channel (name VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, UNIQUE INDEX name (name), PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE schedule_log (id CHAR(36) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', stream_schedule_id CHAR(36) DEFAULT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', last_run_successful TINYINT(1) DEFAULT NULL, message LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, time_executed DATETIME NOT NULL, INDEX IDX_12F62DCFA35860F2 (stream_schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE stream_schedule (id CHAR(36) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', name VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, last_execution DATETIME DEFAULT NULL, disabled TINYINT(1) NOT NULL, wrecked TINYINT(1) NOT NULL, execution_day INT DEFAULT NULL, execution_time TIME DEFAULT NULL, onetime_execution_date DATETIME DEFAULT NULL, stream_duration INT NOT NULL, is_running TINYINT(1) NOT NULL, channel VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE schedule_log ADD CONSTRAINT FK_12F62DCFA35860F2 FOREIGN KEY (stream_schedule_id) REFERENCES stream_schedule (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX UNIQ_C2502824F85E0677 ON app_users');
        $this->addSql('DROP INDEX UNIQ_C2502824E7927C74 ON app_users');
        $this->addSql('ALTER TABLE app_users ADD username_canonical VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, ADD email_canonical VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, ADD salt VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD last_login DATETIME DEFAULT NULL, ADD confirmation_token VARCHAR(180) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD password_requested_at DATETIME DEFAULT NULL, ADD locale VARCHAR(5) NOT NULL COLLATE utf8mb4_unicode_ci, ADD channel VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE username username VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE password password VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE email email VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE is_active enabled TINYINT(1) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C250282492FC23A8 ON app_users (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2502824C05FB297 ON app_users (confirmation_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2502824A0D96FBF ON app_users (email_canonical)');
        $this->addSql('DROP TABLE channel');
    }
}
