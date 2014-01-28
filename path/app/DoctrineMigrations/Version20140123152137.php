<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140123152137 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP INDEX CC1 ON world_country");
        $this->addSql("ALTER TABLE world_country CHANGE id id INT NOT NULL, CHANGE LO LO VARCHAR(3) NOT NULL, CHANGE REGION_ID REGION_ID INT NOT NULL, CHANGE ISO_ALPHA2 ISO_ALPHA2 VARCHAR(2) NOT NULL, CHANGE ISO_NUMERIC ISO_NUMERIC SMALLINT NOT NULL, CHANGE INTERNET INTERNET VARCHAR(2) NOT NULL, CHANGE ISO_4217 ISO_4217 VARCHAR(3) NOT NULL, CHANGE CC2 CC2 VARCHAR(2) NOT NULL, CHANGE COMMENT COMMENT VARCHAR(255) NOT NULL");
        $this->addSql("ALTER TABLE world_regions ADD id INT AUTO_INCREMENT NOT NULL, CHANGE REGION_ID REGION_ID INT NOT NULL, CHANGE REGION_PARENT REGION_PARENT VARCHAR(3) NOT NULL, CHANGE REGION_NAME REGION_NAME VARCHAR(56) NOT NULL, CHANGE LC LC VARCHAR(3) NOT NULL, CHANGE CHARACTERS CHARACTERS VARCHAR(18) NOT NULL, ADD PRIMARY KEY (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE world_country CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE LO LO CHAR(3) DEFAULT 'eng' NOT NULL, CHANGE REGION_ID REGION_ID INT UNSIGNED NOT NULL, CHANGE ISO_ALPHA2 ISO_ALPHA2 CHAR(2) NOT NULL, CHANGE ISO_4217 ISO_4217 CHAR(3) NOT NULL, CHANGE ISO_NUMERIC ISO_NUMERIC TINYINT(1) NOT NULL, CHANGE INTERNET INTERNET CHAR(2) NOT NULL, CHANGE CC2 CC2 CHAR(2) NOT NULL, CHANGE COMMENT COMMENT LONGTEXT NOT NULL");
        $this->addSql("CREATE INDEX CC1 ON world_country (CC1)");
        $this->addSql("ALTER TABLE world_regions MODIFY id INT NOT NULL");
        $this->addSql("ALTER TABLE world_regions DROP PRIMARY KEY");
        $this->addSql("ALTER TABLE world_regions DROP id, CHANGE REGION_ID REGION_ID INT DEFAULT NULL, CHANGE REGION_PARENT REGION_PARENT VARCHAR(3) DEFAULT NULL, CHANGE REGION_NAME REGION_NAME VARCHAR(56) DEFAULT NULL, CHANGE LC LC VARCHAR(3) DEFAULT NULL, CHANGE CHARACTERS CHARACTERS VARCHAR(18) DEFAULT NULL");
    }
}
