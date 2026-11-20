<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261111155334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, subject VARCHAR(255) NOT NULL, msg LONGTEXT NOT NULL, status TINYINT NOT NULL, received_at DATETIME NOT NULL, opened_at DATETIME DEFAULT NULL, updated_by_id INT DEFAULT NULL, INDEX IDX_B6BD307F896DBBDE (updated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notice (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, msg VARCHAR(255) NOT NULL, status TINYINT NOT NULL, received_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, updated_by_id INT DEFAULT NULL, INDEX IDX_480D45C2896DBBDE (updated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE room_category (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, price_per_night DOUBLE PRECISION NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, INDEX IDX_A6AAD905B03A8386 (created_by_id), INDEX IDX_A6AAD905896DBBDE (updated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE room_category_preference (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE room_category_preference_room_category (room_category_preference_id INT NOT NULL, room_category_id INT NOT NULL, INDEX IDX_F0F0BAFD569F387F (room_category_preference_id), INDEX IDX_F0F0BAFD67333DD (room_category_id), PRIMARY KEY (room_category_preference_id, room_category_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, status TINYINT NOT NULL, last_connection_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C2896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE room_category ADD CONSTRAINT FK_A6AAD905B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE room_category ADD CONSTRAINT FK_A6AAD905896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE room_category_preference_room_category ADD CONSTRAINT FK_F0F0BAFD569F387F FOREIGN KEY (room_category_preference_id) REFERENCES room_category_preference (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_category_preference_room_category ADD CONSTRAINT FK_F0F0BAFD67333DD FOREIGN KEY (room_category_id) REFERENCES room_category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F896DBBDE');
        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C2896DBBDE');
        $this->addSql('ALTER TABLE room_category DROP FOREIGN KEY FK_A6AAD905B03A8386');
        $this->addSql('ALTER TABLE room_category DROP FOREIGN KEY FK_A6AAD905896DBBDE');
        $this->addSql('ALTER TABLE room_category_preference_room_category DROP FOREIGN KEY FK_F0F0BAFD569F387F');
        $this->addSql('ALTER TABLE room_category_preference_room_category DROP FOREIGN KEY FK_F0F0BAFD67333DD');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE notice');
        $this->addSql('DROP TABLE room_category');
        $this->addSql('DROP TABLE room_category_preference');
        $this->addSql('DROP TABLE room_category_preference_room_category');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
