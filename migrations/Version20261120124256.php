<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261120124256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE room_picture (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, alt VARCHAR(50) NOT NULL, position INT NOT NULL, created_at DATETIME NOT NULL, room_category_id INT DEFAULT NULL, INDEX IDX_FAF9F36967333DD (room_category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE room_picture ADD CONSTRAINT FK_FAF9F36967333DD FOREIGN KEY (room_category_id) REFERENCES room_category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room_picture DROP FOREIGN KEY FK_FAF9F36967333DD');
        $this->addSql('DROP TABLE room_picture');
    }
}
