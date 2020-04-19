<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200419114358 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE daily_stat (id INT AUTO_INCREMENT NOT NULL, daily_change_id INT DEFAULT NULL, day DATE NOT NULL, total INT DEFAULT 0 NOT NULL, deaths INT DEFAULT 0 NOT NULL, recovered INT DEFAULT 0 NOT NULL, new_cases INT DEFAULT 0 NOT NULL, new_deaths INT DEFAULT 0 NOT NULL, new_recovered INT DEFAULT NULL, serious INT DEFAULT 0 NOT NULL, active INT DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_64BEE0B4E5A02990 (day), UNIQUE INDEX UNIQ_64BEE0B4A03892EE (daily_change_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, updated_at DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE daily_change (id INT AUTO_INCREMENT NOT NULL, day_id INT DEFAULT NULL, new INT NOT NULL, new_percent DOUBLE PRECISION NOT NULL, deaths INT NOT NULL, deaths_percent DOUBLE PRECISION NOT NULL, recovered INT NOT NULL, recovered_percent DOUBLE PRECISION NOT NULL, serious INT NOT NULL, serious_percent DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_C169C98C9C24126 (day_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country_cases (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, cases_change_id INT DEFAULT NULL, day DATE NOT NULL, total INT NOT NULL, deaths INT NOT NULL, recovered INT NOT NULL, new_deaths INT NOT NULL, new_cases INT NOT NULL, new_recovered INT DEFAULT NULL, serious INT NOT NULL, active INT NOT NULL, total_per1m INT NOT NULL, INDEX IDX_BBFB1661F92F3E70 (country_id), UNIQUE INDEX UNIQ_BBFB1661CA673F1D (cases_change_id), UNIQUE INDEX UNIQ_BBFB1661E5A02990F92F3E70 (day, country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cases_change (id INT AUTO_INCREMENT NOT NULL, case_id INT DEFAULT NULL, country_id INT DEFAULT NULL, day DATE NOT NULL, new INT NOT NULL, new_percent DOUBLE PRECISION NOT NULL, deaths INT NOT NULL, deaths_percent DOUBLE PRECISION NOT NULL, recovered INT NOT NULL, recovered_percent DOUBLE PRECISION NOT NULL, serious INT NOT NULL, serious_percent DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_B81E9BB1CF10D4F5 (case_id), INDEX IDX_B81E9BB1F92F3E70 (country_id), UNIQUE INDEX UNIQ_B81E9BB1E5A02990F92F3E70 (day, country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE daily_stat ADD CONSTRAINT FK_64BEE0B4A03892EE FOREIGN KEY (daily_change_id) REFERENCES daily_change (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE daily_change ADD CONSTRAINT FK_C169C98C9C24126 FOREIGN KEY (day_id) REFERENCES daily_stat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE country_cases ADD CONSTRAINT FK_BBFB1661F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE country_cases ADD CONSTRAINT FK_BBFB1661CA673F1D FOREIGN KEY (cases_change_id) REFERENCES cases_change (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cases_change ADD CONSTRAINT FK_B81E9BB1CF10D4F5 FOREIGN KEY (case_id) REFERENCES country_cases (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cases_change ADD CONSTRAINT FK_B81E9BB1F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE daily_change DROP FOREIGN KEY FK_C169C98C9C24126');
        $this->addSql('ALTER TABLE country_cases DROP FOREIGN KEY FK_BBFB1661F92F3E70');
        $this->addSql('ALTER TABLE cases_change DROP FOREIGN KEY FK_B81E9BB1F92F3E70');
        $this->addSql('ALTER TABLE daily_stat DROP FOREIGN KEY FK_64BEE0B4A03892EE');
        $this->addSql('ALTER TABLE cases_change DROP FOREIGN KEY FK_B81E9BB1CF10D4F5');
        $this->addSql('ALTER TABLE country_cases DROP FOREIGN KEY FK_BBFB1661CA673F1D');
        $this->addSql('DROP TABLE daily_stat');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE daily_change');
        $this->addSql('DROP TABLE country_cases');
        $this->addSql('DROP TABLE cases_change');
    }
}
