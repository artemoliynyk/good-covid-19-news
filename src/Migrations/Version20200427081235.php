<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200427081235 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_64BEE0B4E5A02990 ON daily_stat');
        $this->addSql('ALTER TABLE daily_stat CHANGE day daily_date DATE NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64BEE0B4EE9828EF ON daily_stat (daily_date)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_64BEE0B4EE9828EF ON daily_stat');
        $this->addSql('ALTER TABLE daily_stat CHANGE daily_date day DATE NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64BEE0B4E5A02990 ON daily_stat (day)');
    }
}
