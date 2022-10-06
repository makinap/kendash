<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221006021955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attention ADD COLUMN attented_on DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__attention AS SELECT id, ping_id, agent, status, registered_on FROM attention');
        $this->addSql('DROP TABLE attention');
        $this->addSql('CREATE TABLE attention (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ping_id INTEGER DEFAULT NULL, agent VARCHAR(255) NOT NULL, status VARCHAR(1) NOT NULL, registered_on DATETIME NOT NULL, CONSTRAINT FK_823418D470C4B73 FOREIGN KEY (ping_id) REFERENCES ping (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO attention (id, ping_id, agent, status, registered_on) SELECT id, ping_id, agent, status, registered_on FROM __temp__attention');
        $this->addSql('DROP TABLE __temp__attention');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_823418D470C4B73 ON attention (ping_id)');
    }
}
