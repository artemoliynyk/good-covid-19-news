<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200426103515 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_BBFB1661E5A02990F92F3E70 ON country_cases');
        $this->addSql('ALTER TABLE country_cases CHANGE day case_date DATE NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BBFB16612B86EDC9F92F3E70 ON country_cases (case_date, country_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_BBFB16612B86EDC9F92F3E70 ON country_cases');
        $this->addSql('ALTER TABLE country_cases CHANGE case_date day DATE NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BBFB1661E5A02990F92F3E70 ON country_cases (day, country_id)');
    }
}
