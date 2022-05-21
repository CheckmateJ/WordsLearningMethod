<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220211230450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course ADD name VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX IDX_B469456FCD8F897F ON translation');
        $this->addSql('ALTER TABLE translation CHANGE course_type_id course_id INT NOT NULL');
        $this->addSql('ALTER TABLE translation ADD CONSTRAINT FK_B469456F591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('CREATE INDEX IDX_B469456F591CC992 ON translation (course_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE course_type (id INT AUTO_INCREMENT NOT NULL, course_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_447C8A2F591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE course_type ADD CONSTRAINT FK_447C8A2F591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE course DROP name');
        $this->addSql('ALTER TABLE translation DROP FOREIGN KEY FK_B469456F591CC992');
        $this->addSql('DROP INDEX IDX_B469456F591CC992 ON translation');
        $this->addSql('ALTER TABLE translation CHANGE course_id course_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE translation ADD CONSTRAINT FK_B469456FCD8F897F FOREIGN KEY (course_type_id) REFERENCES course_type (id)');
        $this->addSql('CREATE INDEX IDX_B469456FCD8F897F ON translation (course_type_id)');
    }
}
