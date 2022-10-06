<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221005234514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attention (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ping_id INTEGER DEFAULT NULL, agent VARCHAR(255) NOT NULL, status VARCHAR(1) NOT NULL, registered_on DATETIME NOT NULL, CONSTRAINT FK_823418D470C4B73 FOREIGN KEY (ping_id) REFERENCES ping (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_823418D470C4B73 ON attention (ping_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE attention');
    }
}
