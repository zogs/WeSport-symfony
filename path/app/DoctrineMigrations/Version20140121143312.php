<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140121143312 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE sports_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, icon VARCHAR(25) NOT NULL, UNIQUE INDEX UNIQ_566F4792989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sports_sport (id INT AUTO_INCREMENT NOT NULL, sports_category INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, icon VARCHAR(25) NOT NULL, action VARCHAR(15) NOT NULL, UNIQUE INDEX UNIQ_FC7F8407989D9B62 (slug), INDEX IDX_FC7F8407566F4792 (sports_category), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE sports_sport ADD CONSTRAINT FK_FC7F8407566F4792 FOREIGN KEY (sports_category) REFERENCES sports_category (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE sports_sport DROP FOREIGN KEY FK_FC7F8407566F4792");
        $this->addSql("DROP TABLE sports_category");
        $this->addSql("DROP TABLE sports_sport");
    }
}
