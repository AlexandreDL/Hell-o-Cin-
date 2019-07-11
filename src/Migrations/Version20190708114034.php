<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190708114034 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_user ADD role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E9D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('CREATE INDEX IDX_88BDF3E9D60322AC ON app_user (role_id)');
        $this->addSql('ALTER TABLE job CHANGE department_id department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE team CHANGE movie_id movie_id INT DEFAULT NULL, CHANGE job_id job_id INT DEFAULT NULL, CHANGE person_id person_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_user DROP FOREIGN KEY FK_88BDF3E9D60322AC');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP INDEX IDX_88BDF3E9D60322AC ON app_user');
        $this->addSql('ALTER TABLE app_user DROP role_id');
        $this->addSql('ALTER TABLE job CHANGE department_id department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE team CHANGE movie_id movie_id INT DEFAULT NULL, CHANGE job_id job_id INT DEFAULT NULL, CHANGE person_id person_id INT DEFAULT NULL');
    }
}
