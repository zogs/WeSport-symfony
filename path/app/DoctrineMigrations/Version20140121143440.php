<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140121143440 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE sports_sport DROP FOREIGN KEY FK_FC7F8407566F4792");
        $this->addSql("DROP INDEX IDX_FC7F8407566F4792 ON sports_sport");
        $this->addSql("ALTER TABLE sports_sport CHANGE sports_category category_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE sports_sport ADD CONSTRAINT FK_FC7F840712469DE2 FOREIGN KEY (category_id) REFERENCES sports_category (id)");
        $this->addSql("CREATE INDEX IDX_FC7F840712469DE2 ON sports_sport (category_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE sports_sport DROP FOREIGN KEY FK_FC7F840712469DE2");
        $this->addSql("DROP INDEX IDX_FC7F840712469DE2 ON sports_sport");
        $this->addSql("ALTER TABLE sports_sport CHANGE category_id sports_category INT DEFAULT NULL");
        $this->addSql("ALTER TABLE sports_sport ADD CONSTRAINT FK_FC7F8407566F4792 FOREIGN KEY (sports_category) REFERENCES sports_category (id)");
        $this->addSql("CREATE INDEX IDX_FC7F8407566F4792 ON sports_sport (sports_category)");
    }
}
