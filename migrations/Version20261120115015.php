<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261120115015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_ROOM_CATEGORY_LABEL ON room_category (label)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_PREF ON room_category_preference (label)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_ROOM_CATEGORY_LABEL ON room_category');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_PREF ON room_category_preference');
    }
}
