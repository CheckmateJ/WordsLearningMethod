<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220508193851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course ADD reverse TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE learning_plan CHANGE limit_on_day limit_on_day INT DEFAULT NULL');
        $this->addSql('ALTER TABLE translation ADD repetition INT NOT NULL, ADD next_repetition DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course DROP reverse');
        $this->addSql('ALTER TABLE learning_plan CHANGE limit_on_day limit_on_day INT NOT NULL');
        $this->addSql('ALTER TABLE translation DROP repetition, DROP next_repetition');
    }
}
