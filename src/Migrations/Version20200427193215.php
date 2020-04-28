<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200427193215 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE daily_stat CHANGE daily_date daily_date DATE NOT NULL');
        $this->addSql('ALTER TABLE country CHANGE updated_at updated_at DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE country_cases CHANGE case_date case_date DATE NOT NULL');
        $this->addSql('ALTER TABLE cases_change CHANGE change_date change_date DATE NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cases_change CHANGE change_date change_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE country CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE country_cases CHANGE case_date case_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE daily_stat CHANGE daily_date daily_date DATETIME NOT NULL');
    }
}
