<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220612203638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9A76ED395');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE learning_plan DROP FOREIGN KEY FK_9B0A17E5591CC992');
        $this->addSql('ALTER TABLE learning_plan ADD CONSTRAINT FK_9B0A17E5591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE translation DROP FOREIGN KEY FK_B469456F591CC992');
        $this->addSql('ALTER TABLE translation ADD CONSTRAINT FK_B469456F591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9A76ED395');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE learning_plan DROP FOREIGN KEY FK_9B0A17E5591CC992');
        $this->addSql('ALTER TABLE learning_plan ADD CONSTRAINT FK_9B0A17E5591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE translation DROP FOREIGN KEY FK_B469456F591CC992');
        $this->addSql('ALTER TABLE translation ADD CONSTRAINT FK_B469456F591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
