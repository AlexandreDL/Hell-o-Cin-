<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190709124633 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE movie ADD slug VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE job CHANGE department_id department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE team CHANGE movie_id movie_id INT DEFAULT NULL, CHANGE job_id job_id INT DEFAULT NULL, CHANGE person_id person_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user CHANGE role_id role_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_user CHANGE role_id role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job CHANGE department_id department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE movie DROP slug');
        $this->addSql('ALTER TABLE team CHANGE movie_id movie_id INT DEFAULT NULL, CHANGE job_id job_id INT DEFAULT NULL, CHANGE person_id person_id INT DEFAULT NULL');
    }
}
