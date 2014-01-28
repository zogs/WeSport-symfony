<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140123142555 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE world_regions ADD id INT AUTO_INCREMENT NOT NULL, CHANGE REGION_ID REGION_ID INT NOT NULL, CHANGE REGION_PARENT REGION_PARENT VARCHAR(3) NOT NULL, CHANGE REGION_NAME REGION_NAME VARCHAR(56) NOT NULL, CHANGE LC LC VARCHAR(3) NOT NULL, CHANGE CHARACTERS CHARACTERS VARCHAR(18) NOT NULL, ADD PRIMARY KEY (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE world_regions MODIFY id INT NOT NULL");
        $this->addSql("ALTER TABLE world_regions DROP PRIMARY KEY");
        $this->addSql("ALTER TABLE world_regions DROP id, CHANGE REGION_ID REGION_ID INT DEFAULT NULL, CHANGE REGION_PARENT REGION_PARENT VARCHAR(3) DEFAULT NULL, CHANGE REGION_NAME REGION_NAME VARCHAR(56) DEFAULT NULL, CHANGE LC LC VARCHAR(3) DEFAULT NULL, CHANGE CHARACTERS CHARACTERS VARCHAR(18) DEFAULT NULL");
    }
}
