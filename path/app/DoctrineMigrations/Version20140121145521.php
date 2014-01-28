<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140121145521 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE sports_category CHANGE icon icon VARCHAR(25) DEFAULT NULL");
        $this->addSql("ALTER TABLE sports_sport CHANGE icon icon VARCHAR(25) DEFAULT NULL, CHANGE action action VARCHAR(15) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE sports_category CHANGE icon icon VARCHAR(25) NOT NULL");
        $this->addSql("ALTER TABLE sports_sport CHANGE icon icon VARCHAR(25) NOT NULL, CHANGE action action VARCHAR(15) NOT NULL");
    }
}
