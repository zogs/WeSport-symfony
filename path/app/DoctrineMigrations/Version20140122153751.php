<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140122153751 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE events_event ADD organizer_id INT DEFAULT NULL, DROP city_id, DROP city_name, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE nbmax nbmax SMALLINT DEFAULT NULL, CHANGE phone phone VARCHAR(20) DEFAULT NULL");
        $this->addSql("ALTER TABLE events_event ADD CONSTRAINT FK_8B12C281876C4DDA FOREIGN KEY (organizer_id) REFERENCES users (id)");
        $this->addSql("CREATE INDEX IDX_8B12C281876C4DDA ON events_event (organizer_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE events_event DROP FOREIGN KEY FK_8B12C281876C4DDA");
        $this->addSql("DROP INDEX IDX_8B12C281876C4DDA ON events_event");
        $this->addSql("ALTER TABLE events_event ADD city_id INT NOT NULL, ADD city_name VARCHAR(120) NOT NULL, DROP organizer_id, CHANGE address address VARCHAR(255) NOT NULL, CHANGE nbmax nbmax SMALLINT NOT NULL, CHANGE phone phone VARCHAR(20) NOT NULL");
    }
}
