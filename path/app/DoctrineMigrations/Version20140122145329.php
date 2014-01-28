<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140122145329 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE events_event (id INT AUTO_INCREMENT NOT NULL, serie_id INT DEFAULT NULL, sport_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, date DATE NOT NULL, time TIME NOT NULL, city_id INT NOT NULL, city_name VARCHAR(120) NOT NULL, description LONGTEXT NOT NULL, address VARCHAR(255) NOT NULL, nbmin SMALLINT NOT NULL, nbmax SMALLINT NOT NULL, phone VARCHAR(20) NOT NULL, date_depot DATETIME NOT NULL, confirmed TINYINT(1) NOT NULL, online TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8B12C281989D9B62 (slug), INDEX IDX_8B12C281D94388BD (serie_id), INDEX IDX_8B12C281AC78BCF8 (sport_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE events_participants (event_id INT NOT NULL, user_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_E8FA4B6271F7E88B (event_id), INDEX IDX_E8FA4B62A76ED395 (user_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE events_serie (id INT AUTO_INCREMENT NOT NULL, nboccurence SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE events_event ADD CONSTRAINT FK_8B12C281D94388BD FOREIGN KEY (serie_id) REFERENCES events_serie (id)");
        $this->addSql("ALTER TABLE events_event ADD CONSTRAINT FK_8B12C281AC78BCF8 FOREIGN KEY (sport_id) REFERENCES sports_sport (id)");
        $this->addSql("ALTER TABLE events_participants ADD CONSTRAINT FK_E8FA4B6271F7E88B FOREIGN KEY (event_id) REFERENCES events_event (id)");
        $this->addSql("ALTER TABLE events_participants ADD CONSTRAINT FK_E8FA4B62A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE events_participants DROP FOREIGN KEY FK_E8FA4B6271F7E88B");
        $this->addSql("ALTER TABLE events_event DROP FOREIGN KEY FK_8B12C281D94388BD");
        $this->addSql("DROP TABLE events_event");
        $this->addSql("DROP TABLE events_participants");
        $this->addSql("DROP TABLE events_serie");
    }
}
