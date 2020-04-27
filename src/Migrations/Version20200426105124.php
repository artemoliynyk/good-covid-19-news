<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200426105124 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_B81E9BB1E5A02990F92F3E70 ON cases_change');
        $this->addSql('ALTER TABLE cases_change CHANGE day change_date DATE NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B81E9BB1C9497EC1F92F3E70 ON cases_change (change_date, country_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_B81E9BB1C9497EC1F92F3E70 ON cases_change');
        $this->addSql('ALTER TABLE cases_change CHANGE change_date day DATE NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B81E9BB1E5A02990F92F3E70 ON cases_change (day, country_id)');
    }
}
