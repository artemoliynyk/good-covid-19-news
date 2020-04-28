<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200428112212 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE country CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE country_cases_change RENAME INDEX uniq_b81e9bb1cf10d4f5 TO UNIQ_B4BD17A3CF10D4F5');
        $this->addSql('ALTER TABLE country_cases_change RENAME INDEX idx_b81e9bb1f92f3e70 TO IDX_B4BD17A3F92F3E70');
        $this->addSql('ALTER TABLE country_cases_change RENAME INDEX uniq_b81e9bb1c9497ec1f92f3e70 TO UNIQ_B4BD17A3C9497EC1F92F3E70');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE country CHANGE updated_at updated_at DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE country_cases_change RENAME INDEX uniq_b4bd17a3c9497ec1f92f3e70 TO UNIQ_B81E9BB1C9497EC1F92F3E70');
        $this->addSql('ALTER TABLE country_cases_change RENAME INDEX idx_b4bd17a3f92f3e70 TO IDX_B81E9BB1F92F3E70');
        $this->addSql('ALTER TABLE country_cases_change RENAME INDEX uniq_b4bd17a3cf10d4f5 TO UNIQ_B81E9BB1CF10D4F5');
    }
}
