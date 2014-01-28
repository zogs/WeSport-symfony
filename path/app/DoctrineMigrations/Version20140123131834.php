<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140123131834 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE events_event ADD occurence SMALLINT NOT NULL");
        $this->addSql("ALTER TABLE events_serie ADD date_start DATE NOT NULL, ADD date_end DATE NOT NULL, ADD monday TINYINT(1) NOT NULL, ADD tuesday TINYINT(1) NOT NULL, ADD wednesday TINYINT(1) NOT NULL, ADD thursday TINYINT(1) NOT NULL, ADD friday TINYINT(1) NOT NULL, ADD saturday TINYINT(1) NOT NULL, ADD sunday TINYINT(1) NOT NULL, CHANGE nboccurence occurences SMALLINT NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE events_event DROP occurence");
        $this->addSql("ALTER TABLE events_serie DROP date_start, DROP date_end, DROP monday, DROP tuesday, DROP wednesday, DROP thursday, DROP friday, DROP saturday, DROP sunday, CHANGE occurences nboccurence SMALLINT NOT NULL");
    }
}
